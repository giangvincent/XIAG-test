<?php

namespace App\Controller;

use App\Model;
use App\Storage\DataStorage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController
{
    /**
     * @var DataStorage
     */
    private $storage;

    public function __construct(DataStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param Request $request
     *
     * @Route("/project/{id}", name="project", method="GET")
     */
    public function projectAction(Request $request)
    {
        try {
            $project = $this->storage->getProjectById($request->get('id'));

            // [Architecture] Consistency in Response Handling
            // We use `JsonResponse` instead of manually encoding JSON string with `Response`.
            // This ensures improved consistency across the API and automatic header setting (Content-Type: application/json).
            return new JsonResponse($project);
        } catch (Model\NotFoundException $e) {
            return new JsonResponse('Not found', 404);
        } catch (\Throwable $e) {
            return new JsonResponse('Something went wrong', 500);
        }
    }

    /**
     * @param Request $request
     *
     * @Route("/project/{id}/tasks", name="project-tasks", method="GET")
     */
    public function projectTaskPagerAction(Request $request)
    {
        $tasks = $this->storage->getTasksByProjectId(
            $request->get('id'),
            $request->get('limit'),
            $request->get('offset')
        );

        return new JsonResponse($tasks);
    }

    /**
     * @param Request $request
     *
     * @Route("/project/{id}/tasks", name="project-create-task", method="PUT")
     */
    public function projectCreateTaskAction(Request $request)
    {
        $project = $this->storage->getProjectById($request->get('id'));
        if (!$project) {
            return new JsonResponse(['error' => 'Not found'], 404);
        }

        // [Architecture] Encapsulation & Testability
        // Using `$request->request->all()` instead of `$_REQUEST` decouples the code from global state.
        // It allows mocking the Request object in tests and ensures we strictly use data intended for the request body.
        return new JsonResponse(
            $this->storage->createTask($request->request->all(), $project->getId())
        );
    }
}
