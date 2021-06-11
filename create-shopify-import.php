#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$factory = new \Gunratbe\Gunfire\Factory\DbalRepositoryFactory(getenv('GUNFIRE_DB_URI'));
$service = new \Gunratbe\Gunfire\Service\ShopifyProductImportCsvCreator($factory->getProductRepository());

echo $service->create();