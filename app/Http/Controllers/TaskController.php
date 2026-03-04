<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use Illuminate\Http\Request;
use App\Interfaces\TaskInterface;
use App\Models\Task;

class TaskController extends Controller
{
    protected TaskInterface $taskRepository;
    public function __construct(TaskInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function allProjects()
    {
        $projects = $this->taskRepository->allProjects();
        return view('Tasks.index', compact('projects'));
    }

    public function addTask(TaskRequest $request)
    {
        $task = $this->taskRepository->addTask($request);

        return response()->json([
            'status'  => 'success',
            'message' => 'Task created successfully.',
            'task'    => $task,
        ], 201);
    }
    public function fetchTask($id = null)
    {
        $projects = $this->taskRepository->fetchTask($id);
        return response()->json([
            'status'   => 'success',
            'projects' => $projects,
        ]);
    }

    public function editTask(Task $task)
    {
        return response()->json([
            'status' => 'success',
            'task' => $task
        ]);
    }

    public function updateTask(TaskRequest $request, Task $task)
    {
        $updateTask = $this->taskRepository->updateTask($request, $task);

        return response()->json([
            'status'  => 'success',
            'message' => 'Task updated successfully.',
            'task'    => $updateTask,
        ]);
    }

    public function deleteTask(Task $task)
    {
        $this->taskRepository->deleteTask($task);

        return response()->json([
            'status'  => 'success',
            'message' => 'Task deleted successfully.',
        ]);
    }

    public function updatePriority(Request $request)
    {
        $this->taskRepository->updatePriority($request);

        return response()->json([
            'status' => 'success',
            'message' => 'Task order updated successfully'
        ]);
    }
}
