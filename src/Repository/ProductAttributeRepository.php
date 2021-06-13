<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Repository;

use Gunratbe\Gunfire\Model\Product;
use Gunratbe\Gunfire\Model\ProductAttribute;

interface ProductAttributeRepository
{
    public function loadIntoProduct(Product $product): void;

    /**
     * @param ProductAttribute[] $attributes
     */
    public function insertAll(Product $product, array $attributes): void;
}