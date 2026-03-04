<?php

namespace App\Repository;

use App\Interfaces\TaskInterface;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskRepository implements TaskInterface
{
    public function allProjects()
    {
        $projects = Project::all();
        return $projects;
    }

    public function fetchTask($id = null)
    {
        $query = Project::with(['tasks' => function ($query) {
            $query->orderBy('priority', 'asc');
        }]);

        if ($id) {
            $query->where('id', $id);
        }

        $tasks = $query->get();

        return $tasks;
    }

    public function addTask($request)
    {
        try {
            return DB::transaction(function () use ($request) {
                Task::where('project_id', $request->project_id)
                    ->increment('priority');

                return Task::create([
                    'task_name' => $request->task_name,
                    'project_id' => $request->project_id,
                    'priority' => 1,
                ]);
            });
        } catch (\Throwable $th) {
            Log::error('Add task failrd', [
                'error' => $th->getMessage()
            ]);
            throw $th;
        }
    }

    public function updateTask($request, $task)
    {
        try {
            return DB::transaction(function () use ($request, $task) {
                $task->fill([
                    'task_name'  => $request->task_name,
                    'project_id' => $request->project_id,
                ])->save();
                return $task->fresh();
            });
        } catch (\Throwable $th) {
            Log::error('Update task', ['error' => $th->getMessage()]);
            throw $th;
        }
    }

    public function deleteTask($task)
    {
        try {
            return DB::transaction(function () use ($task) {
                Task::where('project_id', $task->project_id)
                    ->where('priority', '>', $task->priority)
                    ->decrement('priority');
                return $task->delete();
            });
        } catch (\Throwable $th) {
            Log::error('Delete tasks', ['error' => $th->getMessage()]);
            throw $th;
        }
    }

    public function updatePriority($request)
    {
        // dd($request);
        try {
            return DB::transaction(function () use ($request) {
                foreach ($request->tasks as $task) {

                    Task::where('id', $task['id'])
                        ->update(['priority' => $task['priority']]);
                }

                return true;
            });
        } catch (\Throwable $th) {
            Log::error('Update priority', ['error' => $th->getMessage()]);
            throw $th;
        }
    }
}
