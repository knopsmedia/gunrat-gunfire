<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Factory;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Gunratbe\Gunfire\Repository\DbalProductAttributeRepository;
use Gunratbe\Gunfire\Repository\DbalProductImageRepository;
use Gunratbe\Gunfire\Repository\DbalProductRepository;
use Gunratbe\Gunfire\Repository\ProductAttributeRepository;
use Gunratbe\Gunfire\Repository\ProductImageRepository;
use Gunratbe\Gunfire\Repository\ProductRepository;

final class DbalRepositoryFactory implements RepositoryFactory
{
    private Connection $connection;

    public function __construct(string $url)
    {
        $this->connection = DriverManager::getConnection(['url' => $url]);
    }

    public function getProductRepository(): ProductRepository
    {
        return new DbalProductRepository(
            $this->connection,
            $this->getProductImageRepository(),
            $this->getProductAttributeRepository()
        );
    }

    public function getProductImageRepository(): ProductImageRepository
    {
        return new DbalProductImageRepository($this->connection);
    }

    public function getProductAttributeRepository(): ProductAttributeRepository
    {
        return new DbalProductAttributeRepository($this->connection);
    }
}