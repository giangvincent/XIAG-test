<?php

namespace App\Storage;

use App\Model;

class DataStorage
{
    /**
     * @var \PDO
     */
    private $pdo;

    // [Review] Excellent: Dependency Injection used here. This makes the class testable.
    // [Architecture] Dependency Injection (DI)
    // Instead of creating the connection inside the class (Tight Coupling), we ask for it in the constructor (Inversion of Control).
    // This allows us to:
    // 1. Swap the database connection for a Mock object during Unit Testing.
    // 2. Configure the connection centrally in the Application bootstrap, rather than scattered across classes.
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param int $projectId
     * @throws Model\NotFoundException
     */
    public function getProjectById($projectId)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM project WHERE id = :id');
        $stmt->execute(['id' => $projectId]);

        if ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            return new Model\Project($row);
        }

        throw new Model\NotFoundException();
    }

    /**
     * @param int $projectId
     * @param int $limit
     * @param int $offset
     */
    public function getTasksByProjectId(int $projectId, int $limit, int $offset)
    {
        // Ensure limit and offset are integers to prevent SQL issues in older versions,
        // though execute params are safer.
        $stmt = $this->pdo->prepare("SELECT * FROM task WHERE project_id = :project_id LIMIT :limit OFFSET :offset");

        // PDO limit/offset binding can be tricky with string emulation, binding explicitly as INT.
        $stmt->bindValue(':project_id', $projectId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        $tasks = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $tasks[] = new Model\Task($row);
        }

        return $tasks;
    }

    /**
     * @param array $data
     * @param int $projectId
     * @return Model\Task
     */
    public function createTask(array $data, int $projectId)
    {
        // [Security] SQL Injection Prevention
        // We must never concatenate variables directly into the query string.
        // Using Prepared Statements with placeholders (e.g., :title) ensures the database treats input as data, not executable code.
        $data['project_id'] = $projectId;

        // Dynamic insert with prepared statements
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":$col", $columns);

        $sql = sprintf(
            "INSERT INTO task (%s) VALUES (%s)",
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        // Fetch the last inserted ID safely
        $data['id'] = $this->pdo->lastInsertId();

        return new Model\Task($data);
    }
}
