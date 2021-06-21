<?php declare(strict_types=1);

namespace Gunratbe\App\Repository;

use DateTimeInterface;
use Knops\Gunfire\Model\Product;
use Knops\Gunfire\Model\ProductPrice;

interface ProductRepository
{
    public function count(): int;

    /**
     * @return Product[]
     */
    public function getAll(): array;

    /**
     * @param int $offset
     * @param int $count
     * @return Product[]
     */
    public function getPage(int $offset, int $count): array;

    public function findBy(array $criteria, int $count, int $offset = 0, ?array $orderBy = null): array;

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