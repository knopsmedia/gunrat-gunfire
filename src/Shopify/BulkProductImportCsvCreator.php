<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Shopify;

use DateTimeInterface;
use Gunratbe\Gunfire\Model\Product;
use Gunratbe\Gunfire\Model\ProductImage;
use Gunratbe\Gunfire\Repository\ProductRepository;
use League\Csv\Writer;

/**
 * @see https://help.shopify.com/en/manual/products/import-export/using-csv#overwriting-csv-file
 */
final class BulkProductImportCsvCreator
{
    private array $headers = [
        'Handle', 'Title', 'Description', 'Vendor', 'Type', 'Tags', 'Published', 'Option1 Name', 'Option1 Value',
        'Option2 Name', 'Option2 Value', 'Option3 Name', 'Option3 value', 'Variant SKU', 'Variant Grams',
        'Variant Inventory Tracker', 'Variant Inventory Qty', 'Variant Inventory Policy', 'Variant Fulfillment Service',
        'Variant Price', 'Variant Compare At Price', 'Variant Requires Shipping', 'Variant Taxable', 'Variant Barcode',
        'Image Src', 'Image Position', 'Image Alt Text', 'Gift Card', 'Variant Weight Unit',
    ];

    private ProductRepository $productRepository;
    private ?DateTimeInterface $updatedProductsSince = null;
    private string $outputFilename = 'build/shopify-import.csv';

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function setUpdatedProductsSince(DateTimeInterface $updatedProductsSince): void
    {
        $this->updatedProductsSince = $updatedProductsSince;
    }

    public function setOutputFilename(string $outputFilename): void
    {
        $this->outputFilename = $outputFilename;
    }

    public function create(): void
    {
        if (null === $this->updatedProductsSince) {
            $products = $this->productRepository->getAll();
        } else {
            $products = $this->productRepository->findUpdatedProductsSince($this->updatedProductsSince);
        }

        $records = [];
        $counter = 0;
        $batchIndex = 1;
        foreach ($products as $product) {
            if (0 === count($product->getImages())) {
                $records[] = $this->createProductRecord($product);
                $counter++;
            } else {
                foreach ($product->getImages() as $i => $image) {
                    if (0 === $i) {
                        $records[] = $this->createProductRecord($product, $image, $i + 1);
                        $counter++;
                    } else {
                        $records[] = $this->createImageRecord($product, $image, $i + 1);
                    }
                }
            }

            if ($counter >= 5000) {
                $this->write($records, $batchIndex++);
                $records = [];
                $counter = 0;
            }
        }

        if ($records) {
            $this->write($records, $batchIndex);
        }
    }

    protected function write(array $records, int $batchIndex = 1)
    {
        $writer = Writer::createFromString('');
        $writer->insertOne($this->headers);
        $writer->insertAll($records);

        $parts = pathinfo($this->outputFilename);
        $filename = sprintf('%s/%s-%d.%s', $parts['dirname'], $parts['filename'], $batchIndex, $parts['extension']);

        file_put_contents($filename, $writer->toString());
    }

    protected function createImageRecord(Product $product, ProductImage $image, int $position): array
    {
        return [
            $product->getHandle(), // Handle
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
        return [
            $product->getHandle(), // Handle
            $product->getName(), // Title
            strip_tags(str_replace("\r\n", '', $product->getDescription())), // Description
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
            $product->getStockQuantity(), // Variant Inventory Qty
            'deny', // Variant Inventory Policy (deny, continue)
            'manual', // Variant Fulfillment Service
            $product->getPriceAmount(), // Variant Price
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