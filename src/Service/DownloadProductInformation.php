<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Service;

final class DownloadProductInformation extends ProductInformationAbstract
{
    public function download(): void
    {
        $productRepository = $this->getRepositoryFactory()->getProductRepository();

        $products = $this->getGunfire()->getProducts();
        $productRepository->insertAll($products);

        $prices = $this->getGunfire()->getPrices();
        $productRepository->updatePrices($prices);
    }
}