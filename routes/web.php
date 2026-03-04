<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [TaskController::class, 'allProjects']);

Route::prefix('task')->controller(TaskController::class)->group(function () {
    Route::get('/all-projects', 'allProjects')->name('tasks.project');
    Route::get('/fetch-task/{id?}', 'fetchTask')->name('task.fetch');
    Route::post('/add-task', 'addTask')->name('task.add');
    Route::get('/edit-task/{task}', 'editTask')->name('task.edit');
    Route::put('/update-task/{task}', 'updateTask')->name('task.update');
    Route::get('/delete-task/{task}', 'deleteTask')->name('task.delete');
    Route::post('/update-priority', 'updatePriority')->name('task.update.priority');
});
