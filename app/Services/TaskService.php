<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    // Retrieve a paginated list of tasks
    public function getTasks($perPage = 10, $search = null, $sortBy = null, $category, $status = null, $archive_only = false, $user_id)
    {
        $query = Task::query()->where('user_id', '=', $user_id);

        if($archive_only){
            $query->onlyTrashed();
        }
        
        if ($search) {
            $query->whereFullText(['title', 'description'], $search);
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($status === 'completed') {
            $query->where('is_completed', true);
        } elseif ($status === 'pending') {
            $query->where('is_completed', false);
        } elseif ($status === 'archived') {
            $query->onlyTrashed();
        }

        if ($sortBy === 'due_date') {
            $query->orderByDesc('due_date');
        } elseif ($sortBy === 'status') {
            $query->orderBy('is_completed')->orderBy('created_at', 'desc');
        } else {
            $query->orderByDesc('created_at'); // fallback sort
        }

        return $query->paginate($perPage);
    }


    // Create a new task
    public function createTask(User $user, $data)
    {
        return $user->tasks()->create([
            'title' => $data['title'],
            'description' => $data['description'],
            'category' => $data['category'],
            'due_date' => isset($data['due_date']) ? Carbon::parse($data['due_date']) : null,
            'is_completed'=> false
        ]);
    }

    // Update an existing task
    public function updateTask($taskId, $data)
    {
        $task = Task::findOrFail($taskId);
        $task->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'due_date' => isset($data['due_date']) ? Carbon::parse($data['due_date']) : null,
            'category'=>$data['category'],
            'is_completed'=>$data['is_completed']
            
        ]);
        return $task;
    }

    // Soft delete a task
    public function softDeleteTask($taskId)
    {
        $task = Task::findOrFail($taskId);
        $task->delete();
        return $task;
    }

    // Restore a soft-deleted task
    public function restoreTask($taskId)
    {
        $task = Task::withTrashed()->findOrFail($taskId);
        $task->restore();
        return $task;
    }
}
