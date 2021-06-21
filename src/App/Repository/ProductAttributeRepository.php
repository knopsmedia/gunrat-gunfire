<?php declare(strict_types=1);

namespace Gunratbe\App\Repository;

use Knops\Gunfire\Model\Product;
use Knops\Gunfire\Model\ProductAttribute;

interface ProductAttributeRepository
{
    public function loadIntoProduct(Product $product): void;

    /**
     * @param ProductAttribute[] $attributes
     */
    public function insertAll(Product $product, array $attributes): void;
}