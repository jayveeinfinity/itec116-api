<?php
    require_once "db.php";
    require_once "Router.php";
    require_once "CarController.php";
    require_once "ExpenseController.php";

    header("Content-Type: application/json");

    $router = new Router();

    $router->get('/', 'CarController@test');
    $router->get('/cars', 'CarController@index');
    $router->post('/cars', 'CarController@store');
    $router->get('/cars/{id}', 'CarController@show');
    $router->put('/cars/{id}', 'CarController@update');
    $router->delete('/cars/{id}', 'CarController@destroy');

    $router->post('/expenses', 'ExpenseController@store');
    $router->get('/expenses', 'ExpenseController@index');
    $router->get('/expenses/{id}', 'ExpenseController@show');
    $router->delete('/expenses/{id}', 'ExpenseController@destroy');

    $router->handleRequest();
?>