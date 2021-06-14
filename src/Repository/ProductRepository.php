<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Repository;

use DateTimeInterface;
use Gunratbe\Gunfire\Model\Product;
use Gunratbe\Gunfire\Model\ProductPrice;

interface ProductRepository
{
    /**
     * @return Product[]
     */
    public function getAll(): array;

    /**
     * @param DateTimeInterface $since
     * @return Product[]
     */
    public function findUpdatedProductsSince(DateTimeInterface $since): array;

    /**
     * @param Product[] $products
     */
    public function replaceAll(array $products): void;

    /**
     * @param ProductPrice[] $prices
     */
    public function updatePrices(array $prices): void;
}