<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Repository;

use Doctrine\DBAL\Connection;
use Gunratbe\Gunfire\Model\Category;
use Gunratbe\Gunfire\Model\Manufacturer;
use Gunratbe\Gunfire\Model\Product;
use Gunratbe\Gunfire\Model\ProductPrice;

final class DbalProductRepository extends AbstractPdoRepository implements ProductRepository
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
        ]);

        $this->imageRepository = $imageRepository;
        $this->attributeRepository = $attributeRepository;
    }

    public function getAll(): array
    {
        $records = $this->getConnection()->createQueryBuilder()
            ->select('*')
            ->from($this->getTableName())
            ->fetchAllAssociative();

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
    }

    public function insertAll(array $products): void
    {
        foreach ($products as $product) {
            $this->_insert($product);
        }
    }

    private function _insert(Product $product): void
    {
        if ($this->_exists($product->getExternalId())) {
            return;
        }

        $this->getConnection()->insert($this->getTableName(), [
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
        ], $this->getColumnDefinitions());

        $this->imageRepository->insertAll($product->getImages());
        $this->attributeRepository->insertAll($product->getAttributes());
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

    public function updatePrices(array $prices): void
    {
        foreach ($prices as $price) {
            $this->_updatePrice($price);
        }
    }

    private function _updatePrice(ProductPrice $price): void
    {
        $record = [
            'stock_quantity'   => $price->getStockQuantity(),
            'price_amount'   => $price->getPriceAmount(),
            'price_currency' => $price->getPriceCurrency(),
        ];

        $this->getConnection()->update(
            $this->getTableName(),
            $record,
            ['external_id' => $price->getProductExternalId()]
        );
    }
}