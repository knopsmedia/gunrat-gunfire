<?php

use Gunratbe\Gunfire\Service\ShopifyProductImportCsvCreator;

require __DIR__ . '/../vendor/autoload.php';

$service = new ShopifyProductImportCsvCreator();
echo $service->create();
