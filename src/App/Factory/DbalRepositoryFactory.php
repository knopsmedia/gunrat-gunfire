<?php declare(strict_types=1);

namespace Gunratbe\App\Factory;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Gunratbe\App\Repository\DbalProductAttributeRepository;
use Gunratbe\App\Repository\DbalProductImageRepository;
use Gunratbe\App\Repository\DbalProductRepository;
use Gunratbe\App\Repository\ProductAttributeRepository;
use Gunratbe\App\Repository\ProductImageRepository;
use Gunratbe\App\Repository\ProductRepository;

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