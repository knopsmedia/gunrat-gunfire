<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Shopify;

final class InventoryLevelApi
{
    private ApiClient $shopifyApi;

    public function __construct(ApiClient $shopifyApi)
    {
        $this->shopifyApi = $shopifyApi;
    }

    public function getInventoryLevels(array $inventoryItemIds): array
    {
        $inventoryLevels = $this->shopifyApi->request('GET', '/inventory_levels.json', query: [
            'inventory_item_ids' => implode(',', $inventoryItemIds),
        ]);

        return $inventoryLevels->body->inventory_levels;
    }

    public function updateStock(int $inventoryItemId, int $locationId, int $quantityAvailable)
    {
        $this->shopifyApi->request('POST', '/inventory_levels/set.json', [
            'inventory_item_id' => $inventoryItemId,
            'location_id'       => $locationId,
            'available'         => $quantityAvailable,
        ]);
    }
}