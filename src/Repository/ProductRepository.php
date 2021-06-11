<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Repository;

use Gunratbe\Gunfire\Model\Product;
use Gunratbe\Gunfire\Model\ProductPrice;

interface ProductRepository
{
    /**
     * @return Product[]
     */
    public function getAll(): array;

    /**
     * @param Product[] $products
     */
    public function insertAll(array $products): void;

    /**
     * @param ProductPrice[] $prices
     */
    public function updatePrices(array $prices): void;
}