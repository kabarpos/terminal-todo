<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Category;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Task::with(['category', 'platform', 'assignees', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($task) => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'category' => [
                    'id' => $task->category->id,
                    'name' => $task->category->name,
                    'color' => $task->category->color
                ],
                'platform' => $task->platform ? [
                    'id' => $task->platform->id,
                    'name' => $task->platform->name,
                    'icon' => $task->platform->icon
                ] : null,
                'priority' => $task->priority,
                'status' => $task->status,
                'start_date' => $task->start_date?->format('Y-m-d\TH:i'),
                'due_date' => $task->due_date?->format('Y-m-d\TH:i'),
                'completed_at' => $task->completed_at?->format('Y-m-d\TH:i'),
                'assignees' => $task->assignees->map(fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar_url' => $user->avatar_url
                ]),
                'creator' => [
                    'id' => $task->creator->id,
                    'name' => $task->creator->name
                ],
                'created_at' => $task->created_at->format('d M Y H:i')
            ]);

        // Debug statement
        \Log::info('Tasks data:', ['tasks' => $tasks->toArray()]);

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'highlight' => $request->query('highlight')
        ]);
    }

    public function create()
    {
        return Inertia::render('Tasks/Create', [
            'categories' => Category::where('type', 'task')->where('is_active', true)->get(),
            'platforms' => Platform::where('is_active', true)->get(),
            'users' => User::where('status', 'active')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'platform_id' => 'nullable|exists:platforms,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:draft,in_progress,completed,cancelled',
            'start_date' => 'nullable|date',
            'due_date' => 'required|date',
            'assignees' => 'required|array|min:1',
            'assignees.*' => 'exists:users,id'
        ]);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'platform_id' => $request->platform_id,
            'priority' => $request->priority,
            'status' => $request->status,
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
            'created_by' => Auth::id()
        ]);

        $task->assignees()->attach($request->assignees);

        return redirect()->route('tasks.index')
            ->with('message', 'Task berhasil dibuat');
    }

    public function show(Task $task)
    {
        $task->load(['category', 'platform', 'assignees', 'creator', 'comments.user']);

        return Inertia::render('Tasks/Show', [
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'category' => [
                    'id' => $task->category->id,
                    'name' => $task->category->name,
                    'color' => $task->category->color
                ],
                'platform' => $task->platform ? [
                    'id' => $task->platform->id,
                    'name' => $task->platform->name,
                    'icon' => $task->platform->icon
                ] : null,
                'priority' => $task->priority,
                'status' => $task->status,
                'start_date' => $task->start_date?->format('Y-m-d\TH:i:s'),
                'due_date' => $task->due_date?->format('Y-m-d\TH:i:s'),
                'completed_at' => $task->completed_at?->format('Y-m-d\TH:i:s'),
                'assignees' => $task->assignees->map(fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar_url' => $user->avatar_url,
                    'role' => $user->pivot->role
                ]),
                'creator' => [
                    'id' => $task->creator->id,
                    'name' => $task->creator->name
                ],
                'comments' => $task->comments->map(fn ($comment) => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                        'avatar_url' => $comment->user->avatar_url
                    ],
                    'created_at' => $comment->created_at->format('Y-m-d\TH:i:s')
                ]),
                'created_at' => $task->created_at->format('Y-m-d\TH:i:s')
            ]
        ]);
    }

    public function edit(Task $task)
    {
        $task->load(['category', 'platform', 'assignees']);

        return Inertia::render('Tasks/Edit', [
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'category_id' => $task->category_id,
                'platform_id' => $task->platform_id,
                'priority' => $task->priority,
                'status' => $task->status,
                'start_date' => $task->start_date?->format('Y-m-d\TH:i'),
                'due_date' => $task->due_date?->format('Y-m-d\TH:i'),
                'assignees' => $task->assignees->map(fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->name
                ])
            ],
            'categories' => Category::where('type', 'task')->where('is_active', true)->get(),
            'platforms' => Platform::where('is_active', true)->get(),
            'users' => User::where('status', 'active')->get()
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'platform_id' => 'nullable|exists:platforms,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:draft,in_progress,completed,cancelled',
            'start_date' => 'nullable|date',
            'due_date' => 'required|date',
            'assignees' => 'required|array|min:1',
            'assignees.*' => 'exists:users,id'
        ]);

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'platform_id' => $request->platform_id,
            'priority' => $request->priority,
            'status' => $request->status,
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
            'completed_at' => $request->status === 'completed' ? now() : null
        ]);

        $task->assignees()->sync($request->assignees);

        return redirect()->route('tasks.index')
            ->with('message', 'Task berhasil diperbarui');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('message', 'Task berhasil dihapus');
    }

    /**
     * Update the status of the specified task.
     */
    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:draft,in_progress,completed,cancelled'],
        ]);

        $task->update([
            'status' => $request->status,
            'completed_at' => $request->status === 'completed' ? now() : null
        ]);

        $task->load(['category', 'platform', 'assignees', 'creator']);

        return response()->json([
            'success' => true,
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'category' => [
                    'id' => $task->category->id,
                    'name' => $task->category->name,
                    'color' => $task->category->color
                ],
                'platform' => $task->platform ? [
                    'id' => $task->platform->id,
                    'name' => $task->platform->name,
                    'icon' => $task->platform->icon
                ] : null,
                'priority' => $task->priority,
                'status' => $task->status,
                'start_date' => $task->start_date?->format('Y-m-d\TH:i'),
                'due_date' => $task->due_date?->format('Y-m-d\TH:i'),
                'completed_at' => $task->completed_at?->format('Y-m-d\TH:i'),
                'assignees' => $task->assignees->map(fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar_url' => $user->avatar_url
                ]),
                'creator' => [
                    'id' => $task->creator->id,
                    'name' => $task->creator->name
                ],
                'created_at' => $task->created_at->format('d M Y H:i')
            ]
        ]);
    }
} 