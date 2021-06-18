<?php declare(strict_types=1);

namespace Gunratbe\App\Repository;

use Gunratbe\App\Model\Product;
use Gunratbe\App\Model\ProductImage;

interface ProductImageRepository
{
    public function loadIntoProduct(Product $product): void;

    /**
     * @param ProductImage[] $images
     */
    public function insertAll(Product $product, array $images): void;
}