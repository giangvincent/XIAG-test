<?php

namespace App;

class Application
{
    public function run(\App\Storage\DataStorage $storage = null)
    {
        // [Architecture] Composition Root / Bootstrap
        // This is the entry point where we wire up our application's dependencies.
        // By instantiating objects here and passing them to each other, we achieve a flexible, decoupled architecture.

        if (null === $storage) {
            // 1. Configuration (Environment variables allow changing config without touching code)
            $dbHost = getenv('DB_HOST') ?: 'ddev-test-task-backend-db:3306';
            $dbName = getenv('DB_NAME') ?: 'db';
            $dbUser = getenv('DB_USER') ?: 'db';
            $dbPass = getenv('DB_PASS') ?: 'db';

            $pdo = new \PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $storage = new Storage\DataStorage($pdo);
        }

        // 2. Routing (Symfony Routing Component)
        // We use our strict custom loader to read PHP 8 Attributes directly.
        $loader = new \App\Routing\ReflectionRouteLoader();
        $routes = $loader->load(Controller\ProjectController::class);

        $context = new \Symfony\Component\Routing\RequestContext();
        $context->fromRequest(\Symfony\Component\HttpFoundation\Request::createFromGlobals());

        $matcher = new \Symfony\Component\Routing\Matcher\UrlMatcher($routes, $context);

        $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

        try {
            // Match the request path to a route
            $parameters = $matcher->match($request->getPathInfo());

            // Add route parameters (slugs, ids) to the Request attributes
            $request->attributes->add($parameters);

            // Manual Dependency Injection for the Controller
            // In a full framework, a Container would do this.
            $controller = new Controller\ProjectController($storage);

            // The '_controller' parameter contains [ClassName, MethodName] set by our Loader
            $method = $parameters['_controller'][1];

            // Execute
            $response = $controller->$method($request);
        } catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {
            $response = new \Symfony\Component\HttpFoundation\JsonResponse('Not Found', 404);
        } catch (\Throwable $e) {
            $response = new \Symfony\Component\HttpFoundation\JsonResponse('Internal Server Error: ' . $e->getMessage(), 500);
        }

        $response->send();
    }
}
