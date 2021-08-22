<?php declare(strict_types=1);

namespace Gunratbe\Shopify;

use Knops\GunfireClient\Model\Product;
use Knops\GunfireClient\Model\ProductImage;
use Gunratbe\App\Repository\ProductRepository;
use Knops\ShopifyClient\ApiClient;

final class RestfulBulkProductImporter implements BulkProductImporter
{
    private ?\DateTimeInterface $updatedProductsSince = null;

    public function __construct(
        private ProductRepository $productRepository,
        private ApiClient $shopifyApi,
    ) {}

    public function setUpdatedProductsSince(?\DateTimeInterface $updatedProductsSince): void
    {
        $this->updatedProductsSince = $updatedProductsSince;
    }

    public function bulkImport(?string $cursor = null): void
    {
        $offset = 0;
        $batchSize = 100;

        $criteria = ['name_is_empty' => false];

        if ($cursor !== null) {
            $criteria['after_name'] = $cursor;
        }

        if ($this->updatedProductsSince !== null) {
            $criteria['updated_since'] = $this->updatedProductsSince->format('Y-m-d H:i:s');
        }

        $totalProducts = $this->productRepository->countAllBy($criteria);

        while (true) {
            $products = $this->productRepository->findAllBy($criteria, $batchSize, $offset);

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

    public function updateProduct(Product $product): void
    {
        $productApi = $this->shopifyApi->products();
        $apiProduct = $productApi->findOneByHandle($product->getHandle());

        $variantData = [
            'sku'                  => $product->getExternalSku(),
            'price'                => $product->getPriceAmount(),
            'weight'               => $product->getWeightInGrams(),
            'weight_unit'          => 'g',
            'inventory_management' => 'shopify',
        ];

        if (null === $apiProduct) {
            $image2data = function (ProductImage $image) {
                return ['src' => $image->getExternalUrl()];
            };

            $productApi->create([
                'handle'       => $product->getHandle(),
                'sku'          => $product->getExternalSku(),
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
            $productApi->update($apiProduct->id, ['status' => 'draft']);

            return;
        }

        // now has a price -> active
        if ($product->getPriceAmount() && $apiProduct->status !== 'active') {
            $productApi->update($apiProduct->id, ['status' => 'active']);
        }

        $product->setShopifyExternalId($apiProduct->id);
        $apiVariant = $apiProduct->variants[0];

        // echo '<pre>';
        // var_dump($apiProduct->variants[0]);
        // exit;

        if ($apiVariant->sku !== $product->getExternalSku()) {
            $this->shopifyApi->variants()->update($apiVariant->id, ['sku' => $product->getExternalSku()]);
        }

        if ($apiVariant->price != $product->getPriceAmount()
            || $apiVariant->weight != $product->getWeightInGrams()
            || $apiVariant->weight_unit !== 'g'
            || $apiVariant->inventory_management !== 'shopify') {
            $this->shopifyApi->variants()->update($apiVariant->id, $variantData);
        }

        if ($apiVariant->inventory_quantity != $product->getStockQuantity()) {
            $inventoryLevelApi = $this->shopifyApi->inventoryLevels();
            $inventoryLevels = $inventoryLevelApi->getInventoryLevels([$apiVariant->inventory_item_id]);
            $inventoryLevel = $inventoryLevels[0];

            $inventoryLevelApi->updateStock(
                $inventoryLevel->inventory_item_id,
                $inventoryLevel->location_id,
                $product->getStockQuantity()
            );
        }
    }
}