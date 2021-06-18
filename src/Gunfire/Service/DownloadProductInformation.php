<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Service;

final class DownloadProductInformation extends ProductInformationAbstract
{
    public function download(): void
    {
        $this->getGunfire()->getPrices();
        $productRepository = $this->getRepositoryFactory()->getProductRepository();

        $products = $this->getGunfire()->getProducts();
        $productRepository->replaceAll($products);

        $prices = $this->getGunfire()->getPrices();
        $productRepository->updatePrices($prices);
    }
}