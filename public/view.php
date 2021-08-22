<?php

require __DIR__ . '/../vendor/autoload.php';

(new \Symfony\Component\Dotenv\Dotenv())->loadEnv(__DIR__ . '/../.env');

$factory = new \Gunratbe\App\Factory\DbalRepositoryFactory($_ENV['GUNRAT_DB_URI']);
$twig = new Twig\Environment(new Twig\Loader\FilesystemLoader(__DIR__ . '/../templates'));

$twig->display('products/view.twig', [
    'product' => $factory->getProductRepository()->findByExternalId($_GET['id']),
    'returnUrl' => $_GET['returnUrl']
]);