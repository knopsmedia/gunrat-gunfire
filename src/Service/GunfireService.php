<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Service;

use Gunratbe\Gunfire\Serializer\ProductXmlDeserializer;
use Sabre\Xml\Reader;
use function Sabre\Xml\Deserializer\repeatingElements;

final class GunfireService
{
    private string $location = __DIR__ . '/../gunfire-products.xml';

    public function getProducts(): array
    {
        $service = new Reader();
        $service->elementMap = [
            '{}offer' => function(Reader $reader) {
                $products = $reader->parseGetElements();

                return $products[0]['value'];
            },
            '{}products' => function (Reader $reader) {
                $reader->elementMap['{}product'] = ProductXmlDeserializer::class;

                return repeatingElements($reader, '{}product');
            },
        ];

        $service->open($this->location);

        return $service->parse()['value'];
    }
}