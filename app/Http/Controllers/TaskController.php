<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Category;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['category', 'platform', 'assignedTo', 'createdBy'])->paginate(10);
        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'platform_id' => 'required|exists:platforms,id',
            'assigned_to' => 'required|exists:users,id',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,review,completed'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'category_id' => $request->category_id,
            'platform_id' => $request->platform_id,
            'assigned_to' => $request->assigned_to,
            'created_by' => Auth::id(),
            'priority' => $request->priority,
            'status' => $request->status
        ]);

        return response()->json($task, 201);
    }

    public function show(Task $task)
    {
        $task->load(['category', 'platform', 'assignedTo', 'createdBy', 'comments']);
        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'string',
            'due_date' => 'date',
            'category_id' => 'exists:categories,id',
            'platform_id' => 'exists:platforms,id',
            'assigned_to' => 'exists:users,id',
            'priority' => 'in:low,medium,high',
            'status' => 'in:pending,in_progress,review,completed'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $task->update($request->all());
        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(null, 204);
    }
} 