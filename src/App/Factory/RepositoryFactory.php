<?php declare(strict_types=1);

namespace Gunratbe\App\Factory;

use Gunratbe\App\Repository\ProductAttributeRepository;
use Gunratbe\App\Repository\ProductImageRepository;
use Gunratbe\App\Repository\ProductRepository;

interface RepositoryFactory
{
    public function getProductRepository(): ProductRepository;

    public function getProductImageRepository(): ProductImageRepository;

    public function getProductAttributeRepository(): ProductAttributeRepository;
}