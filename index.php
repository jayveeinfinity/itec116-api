<?php
    require_once "db.php";
    require_once "Router.php";
    require_once "CarController.php";

    header("Content-Type: application/json");

    $router = new Router();

    $router->get('/', 'CarController@test');
    $router->get('/cars', 'CarController@index');
    $router->post('/cars', 'CarController@store');
    $router->get('/cars/{id}', 'CarController@show');
    $router->put('/cars/{id}', 'CarController@update');
    $router->delete('/cars/{id}', 'CarController@destroy');

    $router->handleRequest();
?>