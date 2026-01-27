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

        // Simple Router
        $controller = new Controller\ProjectController($storage);

        $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
        $response = null;

        $path = $request->getPathInfo();
        if (preg_match('#^/project/(\d+)$#', $path, $matches) && $request->getMethod() === 'GET') {
            $request->attributes->set('id', $matches[1]);
            $response = $controller->projectAction($request);
        } elseif (preg_match('#^/project/(\d+)/tasks$#', $path, $matches)) {
            $request->attributes->set('id', $matches[1]);
            if ($request->getMethod() === 'GET') {
                $response = $controller->projectTaskPagerAction($request);
            } elseif ($request->getMethod() === 'POST') {
                $response = $controller->projectCreateTaskAction($request);
            }
        }

        if ($response) {
            $response->send();
        } else {
            http_response_code(404);
            echo "Not Found";
        }
    }
}
