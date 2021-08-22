<?php declare(strict_types=1);

namespace Gunratbe\App\Repository;

use DateTimeInterface;
use Knops\GunfireClient\Model\Product;
use Knops\GunfireClient\Model\ProductPrice;

interface ProductRepository
{
    public function count(): int;

    public function countAllBy(array $criteria): int;

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

    public function findByExternalId(int $externalId): ?Product;

    public function findAllBy(array $criteria, int $count, int $offset = 0, array $orderBy = []): array;

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