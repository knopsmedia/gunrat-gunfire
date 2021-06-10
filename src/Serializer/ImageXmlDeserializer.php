<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Serializer;

use Gunratbe\Gunfire\Model\Image;
use Sabre\Xml\Reader;
use Sabre\Xml\XmlDeserializable;

final class ImageXmlDeserializer implements XmlDeserializable
{
    public static function xmlDeserialize(Reader $reader)
    {
        $attributes = $reader->parseAttributes();
        $reader->next();

        return new Image($attributes['url']);
    }
}