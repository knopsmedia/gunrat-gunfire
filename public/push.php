<?php

require __DIR__ . '/../vendor/autoload.php';

(new \Symfony\Component\Dotenv\Dotenv())->loadEnv(__DIR__ . '/../.env');

$factory = new \Gunratbe\App\Factory\DbalRepositoryFactory($_ENV['GUNRAT_DB_URI']);
$shopifyApi = new \Knops\ShopifyClient\ApiClient($_ENV['SHOPIFY_SHOP_URL'], $_ENV['SHOPIFY_API_ACCESS_TOKEN']);
$productRepository = $factory->getProductRepository();
$importer = new \Gunratbe\Shopify\RestfulBulkProductImporter($productRepository, $shopifyApi);

$product = $productRepository->findByExternalId((int)$_GET['id']);

if (null !== $product) {
    $importer->updateProduct($product);
}

header('Location: /view.php?id=' . $product->getExternalId() . '&returnUrl=' . $_GET['returnUrl']);