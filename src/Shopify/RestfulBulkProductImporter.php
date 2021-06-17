<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Shopify;

use Gunratbe\Gunfire\Model\Product;
use Gunratbe\Gunfire\Model\ProductImage;
use Gunratbe\Gunfire\Repository\ProductRepository;

final class RestfulBulkProductImporter implements BulkProductImporter
{
    private ProductRepository $productRepository;
    private ProductApi $productApi;
    private ProductVariantApi $variantApi;
    private InventoryLevelApi $inventoryLevelApi;

    public function __construct(ProductRepository $productRepository, ApiClient $shopifyApi)
    {
        $this->productRepository = $productRepository;
        $this->productApi = $shopifyApi->products();
        $this->variantApi = $shopifyApi->variants();
        $this->inventoryLevelApi = $shopifyApi->inventoryLevels();
    }

    public function bulkImport(?string $cursor = null)
    {
        $offset = 0;
        $batchSize = 100;
        $totalProducts = $this->productRepository->count();

        while (true) {
            if (null === $cursor) {
                $products = $this->productRepository->getPage($offset, $batchSize);
            } else {
                $products = $this->productRepository->findBy(['after_name' => $cursor], $batchSize, $offset);
            }

            if (empty($products)) {
                break; // reached the end!
            }

            foreach ($products as $product) {
                $this->updateProduct($product);
                $offset++;

                echo $offset, ' of ', $totalProducts, ' products completed.', PHP_EOL;
            }
        }
    }

    protected function updateProduct(Product $product): void
    {
        $apiProduct = $this->productApi->findOneByHandle($product->getHandle());

        $variantData = [
            'price'                => $product->getPriceAmount(),
            'weight'               => $product->getWeightInGrams(),
            'weight_unit'          => 'g',
            'inventory_management' => 'shopify',
        ];

        if (null === $apiProduct) {
            $image2data = function (ProductImage $image) {
                return ['src' => $image->getExternalUrl()];
            };

            $this->productApi->create([
                'handle'       => $product->getHandle(),
                'title'        => $product->getName(),
                'body_html'    => $product->getDescription(),
                'product_type' => $product->getCategory()->getName(),
                'vendor'       => $product->getManufacturer()->getName(),
                'tags'         => $product->getTags(),
                'images'       => array_map($image2data, $product->getImages()),
                'variants'     => [$variantData],
                'status'       => $product->getPriceAmount() ? 'active' : 'draft',
            ]);

            return;
        }

        // no price -> draft
        if (!$product->getPriceAmount() && $apiProduct->status === 'active') {
            $this->productApi->update($apiProduct->id, ['status' => 'draft']);

            return;
        }

        // now has a price -> active
        if ($product->getPriceAmount() && $apiProduct->status !== 'active') {
            $this->productApi->update($apiProduct->id, ['status' => 'active']);
        }

        $product->setShopifyExternalId($apiProduct->id);
        $apiVariant = $apiProduct->variants[0];

        if ($apiVariant->price != $product->getPriceAmount()
            || $apiVariant->weight != $product->getWeightInGrams()
            || $apiVariant->weight_unit !== 'g'
            || $apiVariant->inventory_management !== 'shopify') {
            $this->variantApi->update($apiVariant->id, $variantData);
        }

        if ($apiVariant->inventory_quantity != $product->getStockQuantity()) {
            $inventoryLevels = $this->inventoryLevelApi->getInventoryLevels([$apiVariant->inventory_item_id]);
            $inventoryLevel = $inventoryLevels[0];

            $this->inventoryLevelApi->updateStock(
                $inventoryLevel->inventory_item_id,
                $inventoryLevel->location_id,
                $product->getStockQuantity()
            );
        }
    }
}