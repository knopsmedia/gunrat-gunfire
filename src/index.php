<?php

use Sabre\Xml\Reader;
use function Sabre\Xml\Deserializer\repeatingElements;
use function Sabre\Xml\Deserializer\keyValue;

require __DIR__ . '/vendor/autoload.php';

$service = new Reader();
$service->elementMap = [
  '{}products' => function(Reader $reader) {
    return repeatingElements($reader, '{}product');
  },
  '{}product' => function(Reader $reader) {
    $product = [];
    $attrs = $reader->parseAttributes();
    $product['Handle'] = $attrs['code_producer'];

    $dump = false;

    $children = $reader->parseInnerTree();
    foreach ($children as $child) {
      switch ($child['name']) {
        case '{}producer':
          $product['Vendor'] = $child['value'];
          break;
        case '{}category':
          $product['Type'] = $child['value'];
          break;
        case '{}description':
          $product['Title'] = $child['value']['title'];
          $product['Body (HTML)'] = $child['value']['description'];
          break;
        case '{}parameters':
          $dump = true;
          $product['Variant Grams'] = $child['value']['Weight [g]'];
          $product['Attributes'] = $child['value'];
          $product['Body (HTML)'] .= "\r\n" . '<ul><li>' . implode('</li><li>', array_map(function($value, $label) { return $label . ': ' . $value; }, $child['value'], array_keys($child['value']))) . '</li></ul>';
          break;
        case '{}images':
          $product['Images'] = $child['value'];
          break;
      }
    }
    if ($dump) {
      var_dump($children, $product); exit;
    }
    return $product;
  },
  '{}producer' => function(Reader $reader) {
    $attrs = $reader->parseAttributes();
    $reader->next();

    return $attrs['name'];
  },
  '{}category' => function(Reader $reader) {
    $attrs = $reader->parseAttributes();
    $reader->next();

    return $attrs['name'];
  },
  '{}card' => function(Reader $reader) {
    $attrs = $reader->parseAttributes();
    $reader->next();

    return $attrs['url'];
  },
  '{}description' => function(Reader $reader) {
    $descriptions = keyValue($reader, '');

    return [
      'title' => $descriptions['name'],
      'description' => $descriptions['long_desc'],
    ];
  },
  '{}images' => function(Reader $reader) {
    $element = $reader->parseInnerTree();

    return $element[0]['value'];
  },
  '{}large' => function(Reader $reader) {
    return repeatingElements($reader, '{}image');
  },
  '{}image' => function(Reader $reader) {
    $attrs = $reader->parseAttributes();
    $reader->next();

    return $attrs['url'];
  },
  '{}parameters' => function(Reader $reader) {
    $children = repeatingElements($reader, '{}parameter');
    $parameters = [];
    foreach ($children as $child) {
      $parameters = array_replace($parameters, $child);
    }

    return $parameters;
  },
  '{}parameter' => function(Reader $reader) {
    $attrs = $reader->parseAttributes();
    $children = $reader->parseInnerTree();

    return [$attrs['name'] => $children[0]['value']];
  },
  '{}value' => function(Reader $reader) {
    $attrs = $reader->parseAttributes();
    $reader->next();

    return $attrs['name'];
  },
];

$service->open('gunfire-products.xml');

print_r($service->parse());

