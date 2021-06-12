<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Service;

use Gunratbe\Gunfire\Model\Product;
use Gunratbe\Gunfire\Model\ProductPrice;
use Gunratbe\Gunfire\Serializer\ProductPriceXmlDeserializer;
use Gunratbe\Gunfire\Serializer\ProductXmlDeserializer;
use Sabre\Xml\Reader;
use function Sabre\Xml\Deserializer\repeatingElements;

final class GunfireService
{
    private string $productsFile;
    private string $pricesFile;

    public function __construct(string $productsFile, string $pricesFile)
    {
        $this->productsFile = $productsFile;
        $this->pricesFile = $pricesFile;
    }

    /**
     * @return Product[]
     * @throws \Sabre\Xml\LibXMLException
     */
    public function getProducts(): array
    {
        $reader = new Reader();
        $reader->elementMap = [
            '{}offer'    => function (Reader $reader) {
                $products = $reader->parseGetElements();

                return $products[0]['value'];
            },
            '{}products' => function (Reader $reader) {
                $reader->elementMap['{}product'] = ProductXmlDeserializer::class;

                return repeatingElements($reader, '{}product');
            },
        ];

        $reader->open($this->productsFile);

        return $reader->parse()['value'];
    }

    /**
     * @return ProductPrice[]
     * @throws \Sabre\Xml\LibXMLException
     */
    public function getPrices(): array
    {
        $reader = new Reader();
        $reader->elementMap = [
            '{}offer'   => function (Reader $reader) {
                $elements = $reader->parseGetElements([
                    '{}products' => function(Reader $reader) {
                        $attributes = $reader->parseAttributes();
                        ProductPriceXmlDeserializer::setCurrency($attributes['currency']);

                        $elements = $reader->parseGetElements(['{}product' => ProductPriceXmlDeserializer::class]);
                        $prices = [];

                        foreach ($elements as $price) {
                            $prices[] = $price['value'];
                        }

                        return $prices;
                    },
                ]);

                return $elements[0]['value'];
            },
        ];

        $reader->open($this->pricesFile);

        return $reader->parse()['value'];
    }
}