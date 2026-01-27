<?php

namespace Tests\Storage;

use App\Model\Project;
use App\Model\Task;
use App\Storage\DataStorage;
use App\Model\NotFoundException;
use PHPUnit\Framework\TestCase;

class DataStorageTest extends TestCase
{
    private $pdo;
    private $storage;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(\PDO::class);
        $this->storage = new DataStorage($this->pdo);
    }

    public function testGetProjectByIdReturnsProject()
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['id' => 1]);
        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(['id' => 1, 'name' => 'Test Project']);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        $project = $this->storage->getProjectById(1);
        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals(1, $project->getId());
    }

    public function testGetProjectByIdThrowsExceptionWhenNotFound()
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('fetch')->willReturn(false);

        $this->pdo->method('prepare')->willReturn($stmt);

        $this->expectException(NotFoundException::class);
        $this->storage->getProjectById(999);
    }

    public function testCreateTask()
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())->method('execute');

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        $this->pdo->expects($this->once())
            ->method('lastInsertId')
            ->willReturn('101');

        $task = $this->storage->createTask(['title' => 'New Task'], 1);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals(101, $task->jsonSerialize()['id']);
        $this->assertEquals(1, $task->jsonSerialize()['project_id']);
    }
}
