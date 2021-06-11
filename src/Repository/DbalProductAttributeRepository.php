<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Repository;

use Doctrine\DBAL\Connection;
use Gunratbe\Gunfire\Model\Product;
use Gunratbe\Gunfire\Model\ProductAttribute;

final class DbalProductAttributeRepository extends AbstractPdoRepository implements ProductAttributeRepository
{
    public function __construct(Connection $connection)
    {
        parent::__construct($connection, 'product_attributes', [
            'product_external_id' => 'integer',
            'name'                => 'string',
            'value'               => 'string',
        ]);
    }

    public function loadIntoProduct(Product $product): void
    {
        $records = $this->getConnection()->createQueryBuilder()
            ->select('*')
            ->from($this->getTableName())
            ->where('product_external_id = :external_id')
            ->setParameter('external_id', $product->getExternalId())
            ->fetchAllAssociative();

        foreach ($records as $record) {
            $product->addAttribute(new ProductAttribute($record['name'], $record['value']));
        }
    }

    public function insertAll(array $attributes): void
    {
        foreach ($attributes as $attribute) {
            $this->_insert($attribute);
        }
    }

    private function _exists(ProductAttribute $attribute): bool
    {
        $record = $this->getConnection()->createQueryBuilder()
            ->select('name')
            ->from($this->getTableName())
            ->where('product_external_id = :external_id AND name = :name')
            ->setParameter('external_id', $attribute->getProduct()->getExternalId())
            ->setParameter('name', $attribute->getName())
            ->fetchOne();

        return $record !== false;
    }

    private function _insert(ProductAttribute $attribute): void
    {
        if ($this->_exists($attribute)) {
            return;
        }

        $this->getConnection()->insert($this->getTableName(), [
            'product_external_id' => $attribute->getProduct()->getExternalId(),
            'name'                => $attribute->getName(),
            'value'               => $attribute->getValue(),
        ], $this->getColumnDefinitions());
    }
}