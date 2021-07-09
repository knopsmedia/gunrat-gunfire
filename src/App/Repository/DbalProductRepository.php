<?php declare(strict_types=1);

namespace Gunratbe\App\Repository;

use Doctrine\DBAL\Connection;
use Knops\Gunfire\Model\Category;
use Knops\Gunfire\Model\Manufacturer;
use Knops\Gunfire\Model\Product;
use Knops\Gunfire\Model\ProductPrice;
use Knops\Utilities\Factory\DateTimeFactory;

final class DbalProductRepository extends AbstractDbalRepository implements ProductRepository
{
    private ProductImageRepository $imageRepository;
    private ProductAttributeRepository $attributeRepository;

    public function __construct(Connection                 $connection,
                                ProductImageRepository     $imageRepository,
                                ProductAttributeRepository $attributeRepository)
    {
        parent::__construct($connection, 'products', [
            'external_id'              => 'integer',
            'external_listing_url'     => 'string',
            'name'                     => 'string',
            'description'              => 'string',
            'category_external_id'     => 'integer',
            'category_name'            => 'string',
            'tags'                     => 'simple_array',
            'external_sku'             => 'string',
            'manufacturer_external_id' => 'integer',
            'manufacturer_name'        => 'string',
            'price_amount'             => 'float',
            'price_currency'           => 'string',
            'stock_quantity'           => 'integer',
            'created_at'               => 'datetime',
            'updated_at'               => 'datetime',
        ]);

        $this->imageRepository = $imageRepository;
        $this->attributeRepository = $attributeRepository;
    }

    public function count(): int
    {
        return (int)$this->getConnection()->createQueryBuilder()
            ->select('count(*)')
            ->from($this->getTableName())
            ->fetchOne();
    }

    public function getAll(): array
    {
        $records = $this->getConnection()->createQueryBuilder()
            ->select('*')
            ->from($this->getTableName())
            ->orderBy('name')
            ->fetchAllAssociative();

        return $this->_hydrateAll($records);
    }

    public function getPage(int $offset, int $count): array
    {
        $records = $this->getConnection()->createQueryBuilder()
            ->select('*')
            ->from($this->getTableName())
            ->where('name != :empty')->setParameter('empty', '')
            ->orderBy('name')
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->fetchAllAssociative();

        return $this->_hydrateAll($records);
    }

    public function countBy(array $criteria): int
    {
        return (int)$this->applyFilters($criteria)->select('count(*)')->fetchOne();
    }

    protected function applyFilters(array $criteria)
    {
        $qb = $this->getConnection()->createQueryBuilder()
            ->select('*')
            ->from($this->getTableName());

        foreach ($criteria as $field => $value) {
            switch ($field) {
                case 'name_is_empty':
                    $expr = $qb->expr();
                    $qb->andWhere($expr->{$value ? 'eq' : 'neq'}('name', ':empty'))->setParameter('empty', '');
                    break;
                case 'after_name':
                    // cursor-based pagination
                    $qb->andWhere('name > :after_name')->orderBy('name')->setParameter('after_name', $value);
                    break;
                case 'updated_since':
                    $qb->andWhere('updated_at > :updated_since')->setParameter('updated_since', $value);
                    break;
            }
        }

        return $qb;
    }

    public function findBy(array $criteria, int $count, int $offset = 0, array $orderBy = []): array
    {
        $qb = $this->applyFilters($criteria)
            ->setFirstResult($offset)
            ->setMaxResults($count);

        foreach ($orderBy as $field => $mode) {
            if (is_int($field)) {
                $field = $mode;
                $mode = 'ASC';
            }

            $qb->addOrderBy($field, $mode);
        }

        $records = $qb->fetchAllAssociative();

        return $this->_hydrateAll($records);
    }

    public function findUpdatedProductsSince(\DateTimeInterface $since): array
    {
        $records = $this->getConnection()->createQueryBuilder()
            ->select('*')
            ->from($this->getTableName())
            ->where('name != :empty')->setParameter('empty', '')
            ->andWhere('updated_at > :since')->setParameter('since', $since, 'datetime')
            ->fetchAllAssociative();

        return $this->_hydrateAll($records);
    }

    private function _hydrateAll(array $records): array
    {
        $products = [];
        $categories = [];
        $manufacturers = [];

        foreach ($records as $record) {
            if (!isset($categories[$record['category_external_id']])) {
                $categories[$record['category_external_id']]
                    = new Category((int)$record['category_external_id'], $record['category_name']);
            }

            if (!isset($manufacturers[$record['manufacturer_external_id']])) {
                $manufacturers[$record['manufacturer_external_id']]
                    = new Manufacturer((int)$record['manufacturer_external_id'], $record['manufacturer_name']);
            }

            $products[] = $product = new Product();

            $this->_hydrate($product, $record);
            $product->setCategory($categories[$record['category_external_id']]);
            $product->setManufacturer($manufacturers[$record['manufacturer_external_id']]);

            $this->imageRepository->loadIntoProduct($product);
            $this->attributeRepository->loadIntoProduct($product);
        }

        return $products;
    }

    private function _hydrate(Product $product, array $data): void
    {
        $product->setExternalId((int)$data['external_id']);
        $product->setExternalListingUrl($data['external_listing_url']);
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setTags(explode(',', $data['tags']));
        $product->setExternalSku($data['external_sku']);
        $product->setPriceAmount((float)$data['price_amount']);
        $product->setPriceCurrency($data['price_currency']);
        $product->setStockQuantity((int)$data['stock_quantity']);
        $product->setCreatedAt(DateTimeFactory::createFromFormat('Y-m-d H:i:s', $data['created_at']));

        if ($data['updated_at']) {
            $product->setUpdatedAt(DateTimeFactory::createFromFormat('Y-m-d H:i:s', $data['updated_at']));
        }
    }

    /**
     * @param Product[] $products
     */
    public function replaceAll(array $products): void
    {
        foreach ($products as $product) {
            if ($this->_exists($product->getExternalId())) {
                $this->_update($product);
            } else {
                $this->_insert($product);
            }

            $this->imageRepository->insertAll($product, $product->getImages());
            $this->attributeRepository->insertAll($product, $product->getAttributes());
        }
    }

    private function _exists(int $externalId): bool
    {
        $record = $this->getConnection()->createQueryBuilder()
            ->select('external_id')
            ->from($this->getTableName())
            ->where('external_id = :external_id')
            ->setParameter('external_id', $externalId)
            ->fetchOne();

        return $record !== false;
    }

    private function _update(Product $product)
    {
        $product->setUpdatedAt(DateTimeFactory::now());

        $this->getConnection()->update(
            $this->getTableName(),
            $this->_dehydrate($product),
            ['external_id' => $product->getExternalId()],
            $this->getColumnDefinitions()
        );
    }

    private function _dehydrate(Product $product): array
    {
        $data = [
            'external_id'              => $product->getExternalId(),
            'external_listing_url'     => $product->getExternalListingUrl(),
            'name'                     => $product->getName(),
            'description'              => $product->getDescription(),
            'category_external_id'     => $product->getCategory()->getExternalId(),
            'category_name'            => $product->getCategory()->getName(),
            'tags'                     => $product->getTags(),
            'external_sku'             => $product->getExternalSku(),
            'manufacturer_external_id' => $product->getManufacturer()->getExternalId(),
            'manufacturer_name'        => $product->getManufacturer()->getName(),
            'price_amount'             => $product->getPriceAmount(),
            'price_currency'           => $product->getPriceCurrency(),
            'stock_quantity'           => $product->getStockQuantity(),
        ];

        // only add these if we dehydrate from database
        if ($product->getCreatedAt()) $data['created_at'] = $product->getCreatedAt();
        if ($product->getUpdatedAt()) $data['updated_at'] = $product->getUpdatedAt();

        return $data;
    }

    private function _insert(Product $product): void
    {
        $product->setCreatedAt(DateTimeFactory::now());

        $this->getConnection()->insert(
            $this->getTableName(),
            $this->_dehydrate($product),
            $this->getColumnDefinitions()
        );
    }

    public function updatePrices(array $prices): void
    {
        foreach ($prices as $price) {
            $this->_updatePrice($price);
        }
    }

    private function _updatePrice(ProductPrice $price): void
    {
        $record = [
            'stock_quantity' => $price->getStockQuantity(),
            'price_amount'   => $price->getPriceAmount(),
            'price_currency' => $price->getPriceCurrency(),
            'updated_at'     => DateTimeFactory::now(),
        ];

        $this->getConnection()->update(
            $this->getTableName(),
            $record,
            ['external_id' => $price->getProductExternalId()],
            $this->getColumnDefinitions()
        );
    }
}