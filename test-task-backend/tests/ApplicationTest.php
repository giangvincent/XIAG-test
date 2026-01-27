<?php

namespace Tests;

use App\Application;
use App\Model\Project;
use App\Storage\DataStorage;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    public function testRunHandlesRequest()
    {
        // Mock DataStorage to bypass DB connection
        $storage = $this->createMock(DataStorage::class);
        $project = new Project(['id' => 1, 'name' => 'Test Project']);

        $storage->method('getProjectById')
            ->willReturn($project);

        // Simulate request using global variables (standard for legacy PHP apps)
        // Note: In a real Symfony app, we'd inject Request object, but here Application creates it from globals.
        $_SERVER['REQUEST_URI'] = '/project/1';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        // Capture output
        ob_start();
        $app = new Application();
        $app->run($storage);
        $output = ob_get_clean();

        // Check if output contains JSON
        $this->assertStringContainsString('{"id":1,"name":"Test Project"}', $output);
    }
}
