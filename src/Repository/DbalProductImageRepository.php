<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Repository;

use Doctrine\DBAL\Connection;
use Gunratbe\Gunfire\Model\Product;
use Gunratbe\Gunfire\Model\ProductImage;

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

    public function insertAll(array $images): void
    {
        foreach ($images as $image) {
            $this->_insert($image);
        }
    }

    private function _exists(ProductImage $image): bool
    {
        $record = $this->getConnection()->createQueryBuilder()
            ->select('external_url')
            ->from($this->getTableName())
            ->where('product_external_id = :external_id AND position = :position')
            ->setParameter('external_id', $image->getProduct()->getExternalId())
            ->setParameter('position', $image->getPosition())
            ->fetchOne();

        return $record !== false;
    }

    private function _insert(ProductImage $image): void
    {
        if ($this->_exists($image)) {
            return;
        }

        $this->getConnection()->insert($this->getTableName(), [
            'external_url'        => $image->getExternalUrl(),
            'position'            => $image->getPosition(),
            'product_external_id' => $image->getProduct()->getExternalId(),
        ], $this->getColumnDefinitions());
    }
}