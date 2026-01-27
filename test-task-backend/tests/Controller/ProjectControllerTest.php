<?php

namespace Tests\Controller;

use App\Controller\ProjectController;
use App\Model\Project;
use App\Model\Task;
use App\Storage\DataStorage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class ProjectControllerTest extends TestCase
{
    private $storage;
    private $controller;

    protected function setUp(): void
    {
        $this->storage = $this->createMock(DataStorage::class);
        $this->controller = new ProjectController($this->storage);
    }

    public function testProjectActionReturnsJson()
    {
        $request = new Request();
        $request->attributes->set('id', 1);

        $project = new Project(['id' => 1, 'name' => 'Demo']);
        $this->storage->expects($this->once())
            ->method('getProjectById')
            ->with(1)
            ->willReturn($project);

        $response = $this->controller->projectAction($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals('{"id":1,"name":"Demo"}', $response->getContent());
    }

    public function testProjectCreateTaskAction()
    {
        // Use real Request object, no need to mock data structure
        $request = new Request([], ['title' => 'New Task']);
        $request->attributes->set('id', 1); // Simulate {id} parameter from route

        $project = new Project(['id' => 1, 'name' => 'Demo']);
        $this->storage->expects($this->once())
            ->method('getProjectById')
            ->with(1)
            ->willReturn($project);

        $task = new Task(['id' => 101, 'title' => 'New Task', 'project_id' => 1]);
        $this->storage->expects($this->once())
            ->method('createTask')
            ->with(['title' => 'New Task'], 1)
            ->willReturn($task);

        $response = $this->controller->projectCreateTaskAction($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $decoded = json_decode($response->getContent(), true);
        $this->assertEquals(101, $decoded['id']);
    }
}
