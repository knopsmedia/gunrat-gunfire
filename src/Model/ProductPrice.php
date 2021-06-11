<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Model;

final class ProductPrice
{
    private int $productExternalId;
    private float $priceAmount;
    private string $priceCurrency;

    public function __construct(int $productExternalId, float $priceAmount, string $priceCurrency)
    {
        $this->productExternalId = $productExternalId;
        $this->priceAmount = $priceAmount;
        $this->priceCurrency = $priceCurrency;
    }

    public function getProductExternalId(): int
    {
        return $this->productExternalId;
    }

    public function getPriceAmount(): float
    {
        return $this->priceAmount;
    }

    public function getPriceCurrency(): string
    {
        return $this->priceCurrency;
    }
}