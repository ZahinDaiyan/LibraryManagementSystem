<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function add(string $method, string $path, callable|array $handler): void
    {
        $this->routes[$method][] = [
            'path' => rtrim($path, '/'),
            'handler' => $handler,
        ];
    }

    public function dispatch(Request $request, Response $response): void
    {
        $method = $request->method();
        $uri = rtrim($request->path(), '/');
        if ($uri === '') {
            $uri = '/';
        }

        foreach ($this->routes[$method] ?? [] as $route) {
            if (($params = $this->match($route['path'], $uri)) !== false) {
                $this->invokeHandler($route['handler'], $params, $request, $response);
                return;
            }
        }

        $response->setStatusCode(404);
        echo '404 Not Found';
    }

    protected function match(string $routePath, string $uri): array|false
    {
        $routePath = $routePath === '' ? '/' : $routePath;
        if ($routePath === $uri) {
            return [];
        }

        $routeSegments = explode('/', trim($routePath, '/'));
        $uriSegments = explode('/', trim($uri, '/'));

        if (count($routeSegments) !== count($uriSegments)) {
            return false;
        }

        $params = [];

        foreach ($routeSegments as $index => $segment) {
            if (str_starts_with($segment, '{') && str_ends_with($segment, '}')) {
                $name = trim($segment, '{}');
                $params[$name] = $uriSegments[$index];
                continue;
            }

            if ($segment !== $uriSegments[$index]) {
                return false;
            }
        }

        return $params;
    }

    protected function invokeHandler(callable|array $handler, array $params, Request $request, Response $response): void
    {
        if (is_callable($handler) && !is_array($handler)) {
            call_user_func($handler, $request, $response, $params);
            return;
        }

        [$controllerClass, $method] = $handler;
        if (!class_exists($controllerClass)) {
            throw new \RuntimeException('Controller class not found: ' . $controllerClass);
        }

        $controller = new $controllerClass($request, $response);
        if (!method_exists($controller, $method)) {
            throw new \RuntimeException('Controller method not found: ' . $method);
        }

        $refMethod = new \ReflectionMethod($controllerClass, $method);
        $methodParams = $refMethod->getParameters();
        if (count($methodParams) > 0 && $methodParams[0]->getType() && $methodParams[0]->getType()->getName() === 'array') {
            call_user_func([$controller, $method], $params);
        } else {
            call_user_func_array([$controller, $method], $params);
        }
    }
}
