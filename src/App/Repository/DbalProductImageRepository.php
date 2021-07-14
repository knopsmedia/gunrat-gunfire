<?php declare(strict_types=1);

namespace Gunratbe\App\Repository;

use Doctrine\DBAL\Connection;
use Knops\GunfireClient\Model\Product;
use Knops\GunfireClient\Model\ProductImage;

final class DbalProductImageRepository extends AbstractDbalRepository implements ProductImageRepository
{
    public function __construct(Connection $connection)
    {
        parent::__construct($connection, 'product_images', [
            'product_external_id' => 'integer',
            'position'            => 'integer',
            'external_url'        => 'string',
        ]);
    }

    public function loadIntoProduct(Product $product): void
    {
        $records = $this->getConnection()->createQueryBuilder()
            ->select('*')
            ->from($this->getTableName())
            ->where('product_external_id = :external_id')
            ->orderBy('position')
            ->setParameter('external_id', $product->getExternalId())
            ->fetchAllAssociative();

        foreach ($records as $record) {
            $product->addImage(new ProductImage($record['external_url']), (int)$record['position']);
        }
    }

    public function insertAll(Product $product, array $images): void
    {
        $this->getConnection()->delete($this->getTableName(), [
            'product_external_id' => $product->getExternalId(),
        ]);

        foreach ($images as $image) {
            $this->_insert($image);
        }
    }

    private function _insert(ProductImage $image): void
    {
        $this->getConnection()->insert($this->getTableName(), [
            'external_url'        => $image->getExternalUrl(),
            'position'            => $image->getPosition(),
            'product_external_id' => $image->getProduct()->getExternalId(),
        ], $this->getColumnDefinitions());
    }
}