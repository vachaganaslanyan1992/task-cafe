<?php

declare(strict_types=1);

namespace Core;

/**
 * Simple Router class to register routes and dispatch requests.
 */
class Router
{
    /**
     * @var array<string, array<string, array{string, string}>>
     */
    private array $routes = [];

    /**
     * Registers a GET route.
     *
     * @param string $path
     * @param array $action [ControllerClass, methodName]
     * @return void
     */
    public function get(string $path, array $action): void
    {
        $this->addRoute('GET', $path, $action);
    }

    /**
     * Registers a POST route.
     *
     * @param string $path
     * @param array $action [ControllerClass, methodName]
     * @return void
     */
    public function post(string $path, array $action): void
    {
        $this->addRoute('POST', $path, $action);
    }

    /**
     * Adds a route to the internal routing table.
     *
     * @param string $method HTTP method
     * @param string $path URI path
     * @param array $action [ControllerClass, methodName]
     * @return void
     */
    private function addRoute(string $method, string $path, array $action): void
    {
        $this->routes[$method][$path] = $action;
    }

    /**
     * Dispatches the request to the matched route.
     *
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $uri Request URI
     * @return void
     */
    public function dispatch(string $method, string $uri): void
    {
        $path = rtrim(parse_url($uri, PHP_URL_PATH), '/') ?: '/';

        if (isset($this->routes[$method][$path])) {
            [$controllerClass, $methodName] = $this->routes[$method][$path];

            if (!class_exists($controllerClass)) {
                http_response_code(500);
                echo "Controller $controllerClass not found.";
                return;
            }

            $controller = new $controllerClass();

            if (!method_exists($controller, $methodName)) {
                http_response_code(500);
                echo "Method $methodName not found in controller $controllerClass.";
                return;
            }

            $body = json_decode(file_get_contents("php://input"), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $body = []; // empty array if JSON invalid or missing
            }

            call_user_func([$controller, $methodName], $body);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Route not found']);
        }
    }
}
