<?php declare(strict_types=1);

namespace Gunratbe\App\Repository;

use Knops\GunfireClient\Model\Product;
use Knops\GunfireClient\Model\ProductImage;

interface ProductImageRepository
{
    public function loadIntoProduct(Product $product): void;

    /**
     * @param ProductImage[] $images
     */
    public function insertAll(Product $product, array $images): void;
}