<?php

namespace App\Interfaces;

interface TaskInterface
{
    public function allProjects();
    public function fetchTask($id);
    public function addTask($request);

    public function updateTask($request, $task);
    public function deleteTask($task);
    public function updatePriority($request);
}
