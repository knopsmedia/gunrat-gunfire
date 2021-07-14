<?php declare(strict_types=1);

namespace Gunratbe\App\Repository;

use Knops\GunfireClient\Model\Product;
use Knops\GunfireClient\Model\ProductAttribute;

interface ProductAttributeRepository
{
    public function loadIntoProduct(Product $product): void;

    /**
     * @param ProductAttribute[] $attributes
     */
    public function insertAll(Product $product, array $attributes): void;
}