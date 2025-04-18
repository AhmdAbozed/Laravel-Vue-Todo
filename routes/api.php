<?php
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
//currently no xsrf or sanctum tokens as its set to web guard
Route::middleware(['web', 'auth'])->get('/tasks', [TaskController::class, 'index']); // Get paginated tasks
Route::middleware(['web', 'auth'])->post('tasks', [TaskController::class, 'store']); // Create task
Route::middleware(['web', 'auth'])->put('tasks/{taskId}', [TaskController::class, 'update']); // Update task
Route::middleware(['web', 'auth'])->delete('tasks/{taskId}', [TaskController::class, 'destroy']); // Soft delete task
Route::middleware(['web', 'auth'])->post('tasks/restore/{taskId}', [TaskController::class, 'restore']); // Restore task
