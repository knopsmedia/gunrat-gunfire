<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Serializer;

use Gunratbe\Gunfire\Model\Attribute;
use Sabre\Xml\Reader;
use Sabre\Xml\XmlDeserializable;

final class AttributeXmlDeserializer implements XmlDeserializable
{
    public static function xmlDeserialize(Reader $reader)
    {
        $attributes = $reader->parseAttributes();
        $children = $reader->parseInnerTree([
            '{}value' => function (Reader $reader) {
                $attrs = $reader->parseAttributes();
                $reader->next();

                return $attrs['name'];
            },
        ]);
        $reader->next();

        return new Attribute($attributes['name'], $children[0]['value']);
    }
}