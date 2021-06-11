<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Factory;

use Gunratbe\Gunfire\Repository\ProductAttributeRepository;
use Gunratbe\Gunfire\Repository\ProductImageRepository;
use Gunratbe\Gunfire\Repository\ProductRepository;

interface RepositoryFactory
{
    public function getProductRepository(): ProductRepository;

    public function getProductImageRepository(): ProductImageRepository;

    public function getProductAttributeRepository(): ProductAttributeRepository;
}