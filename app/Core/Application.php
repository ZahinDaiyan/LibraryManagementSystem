<?php

namespace App\Core;

class Application
{
    public Router $router;
    public Request $request;
    public Response $response;
    public string $baseDir;

    public function __construct(string $baseDir)
    {
        $this->baseDir = rtrim($baseDir, '/\\');
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router();
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        $routesFile = $this->baseDir . '/app/Core/routes.php';
        if (!file_exists($routesFile)) {
            throw new \RuntimeException('Routes file not found: ' . $routesFile);
        }

        $router = require $routesFile;
        if ($router instanceof Router) {
            $this->router = $router;
        }
    }

    public function run(): void
    {
        $this->router->dispatch($this->request, $this->response);
    }
}
