<?php
namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    // Get paginated tasks
    public function index(Request $request)
    {
        $search = $request->query('search');
        $sortBy = $request->query('sortBy');
        $category = $request->query('category');
        $status = $request->query('status');
        $archive_only = $request->query('archive_only');
        $tasks = $this->taskService->getTasks(10, $search, $sortBy, $category, $status, $archive_only, $request->user()->id);
        return response()->json($tasks);
    }

    // Create a new task
    public function store(TaskRequest $request)
    {
        $task = $this->taskService->createTask($request->user(), $request->validated());
        return response()->json($task, 201);
    }

    // Update an existing task
    public function update(TaskRequest $request, $taskId)
    {

        $task = $this->taskService->updateTask($taskId, $request->validated());
        return response()->json($task);
    }

    // Soft delete a task
    public function destroy($taskId)
    {
        $task = $this->taskService->softDeleteTask($taskId);
        return response()->json(['message' => 'Task deleted successfully', 'task' => $task]);
    }

    // Restore a soft-deleted task
    public function restore($taskId)
    {
        $task = $this->taskService->restoreTask($taskId);
        return response()->json(['message' => 'Task restored successfully', 'task' => $task]);
    }
}
