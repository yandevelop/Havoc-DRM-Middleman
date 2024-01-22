<?php
    session_start();
    require_once 'base.php';
    require_once base_path('vendor/autoload.php');
    require_once '../Core/Router.php';


    $router = new \Core\Router();
    
    require_once 'routes.php';

    $uri = parse_url($_SERVER['REQUEST_URI'])['path'];
    $method = $_SERVER['REQUEST_METHOD'];

    $router->route($uri, $method);
?>