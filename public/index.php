<?php

require __DIR__ . '/../vendor/autoload.php';

(new \Symfony\Component\Dotenv\Dotenv())->loadEnv(__DIR__ . '/../.env');

$factory = new \Gunratbe\App\Factory\DbalRepositoryFactory($_ENV['GUNRAT_DB_URI']);
$twig = new Twig\Environment(new Twig\Loader\FilesystemLoader(__DIR__ . '/../templates'));
$productRepository = $factory->getProductRepository();

$page = (int)($_GET['page'] ?? 1);
$itemsPerPage = 500;
unset($_GET['page']);

$criteria = $_GET;
$totalProducts = $productRepository->countAllBy($criteria);
$totalPages = ceil($totalProducts / $itemsPerPage);

$page = ($page >= 1) && ($page <= $totalPages) ? $page : 1;

$twig->display('products/index.twig', [
    'products' => $productRepository->findAllBy($criteria, $itemsPerPage, ($page - 1) * $itemsPerPage),
    'productCount' => $totalProducts,
    'pageCount' => $totalPages,
    'page' => $page,
    'returnUrl' => $_SERVER['REQUEST_URI'],
]);