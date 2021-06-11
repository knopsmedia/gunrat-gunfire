<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Repository;

use Gunratbe\Gunfire\Model\Product;
use Gunratbe\Gunfire\Model\ProductImage;

interface ProductImageRepository
{
    public function loadIntoProduct(Product $product): void;

    /**
     * @param ProductImage[] $images
     */
    public function insertAll(array $images): void;
}