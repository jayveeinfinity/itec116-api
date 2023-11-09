<?php

class Router
{
    private $root = '/itec116_api';
    private $routes = [];

    public function get($path, $callback) {
        $this->addRoute('GET', $path, $callback);
    }

    public function post($path, $callback) {
        $this->addRoute('POST', $path, $callback);
    }

    public function put($path, $callback) {
        $this->addRoute('PUT', $path, $callback);
    }

    public function delete($path, $callback) {
        $this->addRoute('DELETE', $path, $callback);
    }

    public function addRoute($method, $path, $callback)
    {
        $pattern = $this->preparePattern($path);

        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'callback' => $callback,
        ];
    }

    public function handleRequest()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // /itec116_bonza/cars-api/products/2
        $path = str_replace($this->root, '', $path);
        // /products/2

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $path, $matches)) {
                // Remove the full match from the matches array
                array_shift($matches);
                $this->callRouteCallback($route['callback'], $matches);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }

    private function preparePattern($pattern)
    {
        $pattern = str_replace('/', '\/', $pattern);
        return '/^' . preg_replace('/\{([^\/]+)\}/', '(?<$1>[^\/]+)', $pattern) . '$/i';
    }

    private function callRouteCallback($callback, $routeParams)
    {
        if (is_string($callback) && strpos($callback, '@') !== false) {
            list($controller, $method) = explode('@', $callback);

            $controllerInstance = new $controller();
            $controllerInstance->$method($routeParams);
        } elseif (is_callable($callback)) {
            call_user_func($callback, $routeParams);
        } else {
            echo json_encode(['error' => "Callback error"]);
        }
    }
}

?>