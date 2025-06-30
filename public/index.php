<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$router = new Router();

$registerRoutes = require __DIR__ . '/../routes/api.php';
$registerRoutes($router);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
