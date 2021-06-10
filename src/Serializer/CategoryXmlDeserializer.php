<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Serializer;

use Gunratbe\Gunfire\Model\Category;
use Sabre\Xml\Reader;
use Sabre\Xml\XmlDeserializable;

final class CategoryXmlDeserializer implements XmlDeserializable
{
    public static function xmlDeserialize(Reader $reader)
    {
        $attributes = $reader->parseAttributes();
        $reader->next();

        return new Category((int)$attributes['id'], $attributes['name']);
    }
}