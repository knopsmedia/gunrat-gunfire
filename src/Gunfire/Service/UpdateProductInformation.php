<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Service;

final class UpdateProductInformation extends ProductInformationAbstract
{
    public function update(): void
    {
        $this->getRepositoryFactory()
             ->getProductRepository()
             ->updatePrices($this->getGunfire()->getPrices());
    }
}