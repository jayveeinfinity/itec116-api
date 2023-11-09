<?php
    require_once "db.php";
    require_once "Router.php";
    require_once "CarController.php";
    require_once "ExpenseController.php";
    require_once "ProductController.php";
    require_once "SalesController.php";

    header("Content-Type: application/json");

    $router = new Router();

    $router->get('/', 'CarController@test');
    $router->get('/cars', 'CarController@index');
    $router->post('/cars', 'CarController@store');
    $router->get('/cars/{id}', 'CarController@show');
    $router->put('/cars/{id}', 'CarController@update');
    $router->delete('/cars/{id}', 'CarController@destroy');

    $router->get('/expenses', 'ExpenseController@index');
    $router->post('/expenses', 'ExpenseController@store');
    $router->get('/expenses/{id}', 'ExpenseController@show');
    $router->delete('/expenses/{id}', 'ExpenseController@destroy');

    $router->get('/products', 'ProductController@index');
    $router->post('/products', 'ProductController@store');
    //$router->put('/products/{id}', 'ProductController@update');
    $router->get('/products/{id}', 'ProductController@show');

    $router->get('/sales', 'SalesController@index');
    $router->post('/sales/{id}', 'SalesController@store');
    $router->post('/sales', 'SalesController@withdraw');
    $router->get('/sales/test/{id}', 'SalesController@test');

    $router->handleRequest();
?>  