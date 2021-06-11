#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$service = new \Gunratbe\Gunfire\Service\DownloadProductInformation(
    new \Gunratbe\Gunfire\Service\GunfireService(),
    new \Gunratbe\Gunfire\Factory\DbalRepositoryFactory(getenv('GUNFIRE_DB_URI'))
);

$service->download();
