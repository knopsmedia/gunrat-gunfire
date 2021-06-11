<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Service;

use Cocur\Slugify\Slugify;
use Gunratbe\Gunfire\Model\ProductImage;
use Gunratbe\Gunfire\Model\Product;
use Gunratbe\Gunfire\Repository\ProductRepository;
use League\Csv\Writer;

/**
 * @see https://help.shopify.com/en/manual/products/import-export/using-csv#overwriting-csv-file
 */
final class ShopifyProductImportCsvCreator
{
    private array $headers = [
        'Handle', 'Title', 'Body (HTML)', 'Vendor', 'Type', 'Tags', 'Published', 'Option1 Name', 'Option1 Value',
        'Option2 Name', 'Option2 Value', 'Option3 Name', 'Option3 value', 'Variant SKU', 'Variant Grams',
        'Variant Inventory Tracker', 'Variant Inventory Qty', 'Variant Inventory Policy', 'Variant Fulfillment Service',
        'Variant Price', 'Variant Compare At Price', 'Variant Requires Shipping', 'Variant Taxable', 'Variant Barcode',
        'Image Src', 'Image Position', 'Image Alt Text', 'Gift Card', 'Variant Weight Unit',
    ];

    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function create(): string
    {
        foreach ($this->productRepository->getAll() as $product) {
            if (0 === count($product->getImages())) {
                $records[] = $this->createProductRecord($product);
            } else {
                foreach ($product->getImages() as $i => $image) {
                    if (0 === $i) {
                        $records[] = $this->createProductRecord($product, $image, $i + 1);
                    } else {
                        $records[] = $this->createImageRecord($product, $image, $i + 1);
                    }
                }
            }
        }

        $writer = Writer::createFromString('');
        $writer->insertOne($this->headers);
        $writer->insertAll($records);

        return $writer->toString();
    }

    protected function createImageRecord(Product $product, ProductImage $image, int $position): array
    {
        $slugify = new Slugify();

        return [
            $slugify->slugify($product->getName()), // Handle
            '', // Title
            '', // Body (HTML)
            '', // Vendor
            '', // Type
            '', // Tags
            '', // Published
            '', // Option 1 Name
            '', // Option 1 Value
            '', // Option 2 Name
            '', // Option 2 Value
            '', // Option 3 Name
            '', // Option 3 Value
            '', // Variant SKU
            '', // Variant Grams
            '', // Variant Inventory Tracker
            '', // Variant Inventory Qty
            '', // Variant Inventory Policy (deny, continue)
            '', // Variant Fulfillment Service
            '', // Variant Price
            '', // Variant Compare At Price
            '', // Variant Requires Shipping
            '', // Variant Taxable
            '', // Variant Barcode
            $image->getExternalUrl(), // Image Src
            $position, // Image Position
            '', // Image Alt Text
            '', // Gift Card
        ];
    }

    protected function createProductRecord(Product $product, ?ProductImage $image = null, int $position = 0): array
    {
        $slugify = new Slugify();

        return [
            $slugify->slugify($product->getName()), // Handle
            $product->getName(), // Title
            '', // str_replace("\r\n", '', $product->getDescription()), // Body (HTML)
            $product->getManufacturer()->getName(), // Vendor
            $product->getCategory()->getName(), // Type
            implode(',', $product->getTags()), // Tags
            'TRUE', // Published
            'Title', // Option 1 Name
            'Default Title', // Option 1 Value
            '', // Option 2 Name
            '', // Option 2 Value
            '', // Option 3 Name
            '', // Option 3 Value
            $product->getExternalSku(), // Variant SKU
            $product->getWeightInKg(), // Variant Grams
            '', // Variant Inventory Tracker
            '', // Variant Inventory Qty
            'deny', // Variant Inventory Policy (deny, continue)
            'manual', // Variant Fulfillment Service
            0, // Variant Price
            '', // Variant Compare At Price
            'TRUE', // Variant Requires Shipping
            'TRUE', // Variant Taxable
            '', // Variant Barcode
            $image ? $image->getExternalUrl() : '', // Image Src
            $image ? $position : '', // Image Position
            $product->getName(), // Image Alt Text
            'FALSE', // Gift Card
        ];
    }
}