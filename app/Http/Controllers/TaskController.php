<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Category;
use App\Models\Platform;
use App\Models\Team;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(Request $request)
    {
        // Ambil waktu mulai untuk logging performa
        $startTime = microtime(true);
        $requestId = uniqid('task_', true);
        
        \Log::info("[TASK] Request index dimulai - ID: $requestId, IP: " . $request->ip());
        
        try {
            // 1. Bersihkan cache untuk memastikan data segar
            \Cache::forget('tasks.all');
            \Cache::forget('tasks.collection');
            \Log::debug("[TASK] Cache dibersihkan untuk request $requestId");
            
            // 2. Ambil data task langsung dari database untuk menghindari masalah cache
            $tasks = \App\Models\Task::with(['category', 'platform', 'team', 'assignees', 'creator'])
                ->orderBy('created_at', 'desc')
                ->get();
                
            // 3. Deduplikasi tasks
            $uniqueTasks = $tasks->unique('id');
            
            // 4. Cek jika ada duplikasi
            if ($uniqueTasks->count() < $tasks->count()) {
                $duplicatesCount = $tasks->count() - $uniqueTasks->count();
                \Log::warning("[TASK] Terdeteksi $duplicatesCount task duplikat saat pengambilan data - Request: $requestId");
                
                // Gunakan tasks yang sudah dideduplikasi
                $tasks = $uniqueTasks;
            }
            
            \Log::info("[TASK] Berhasil mengambil {$tasks->count()} task - Request: $requestId");
            
            // 5. Format data untuk frontend
            $formattedTasks = $tasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'start_date' => $task->start_date?->format('Y-m-d\TH:i'),
                    'due_date' => $task->due_date?->format('Y-m-d\TH:i'),
                    'completed_at' => $task->completed_at?->format('Y-m-d\TH:i'),
                    'category' => $task->category ? [
                        'id' => $task->category->id,
                        'name' => $task->category->name,
                        'color' => $task->category->color
                    ] : null,
                    'platform' => $task->platform ? [
                        'id' => $task->platform->id,
                        'name' => $task->platform->name,
                        'icon' => $task->platform->icon
                    ] : null,
                    'team' => $task->team ? [
                        'id' => $task->team->id,
                        'name' => $task->team->name
                    ] : null,
                    'assignees' => $task->assignees->map(fn ($user) => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'avatar_url' => $user->avatar_url
                    ])->toArray(),
                    'creator' => $task->creator ? [
                        'id' => $task->creator->id,
                        'name' => $task->creator->name
                    ] : null,
                    'created_at' => $task->created_at->format('d M Y H:i')
                ];
            })->toArray();
            
            // 6. Filter berdasarkan status (jika diperlukan)
            if ($request->has('status') && $request->status) {
                $statusFilter = $request->status;
                $formattedTasks = array_values(array_filter($formattedTasks, function ($task) use ($statusFilter) {
                    return $task['status'] === $statusFilter;
                }));
                
                \Log::debug("[TASK] Data difilter berdasarkan status: $statusFilter - Hasil: " . count($formattedTasks) . " task");
            }
            
            // 7. Persiapkan response dengan anti-cache headers
            $response = Inertia::render('Tasks/Index', [
                'tasks' => $formattedTasks,
                'categories' => Category::where('type', 'task')->where('is_active', true)->get(),
                'highlight' => $request->query('highlight'),
                'statusFilter' => $request->query('status'),
                'timestamp' => time(), // Untuk cache busting
                'requestId' => $requestId // Untuk debugging
            ])->toResponse($request);
            
            // 8. Set anti-cache headers
            $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->header('Pragma', 'no-cache');
            $response->header('Expires', '0');
            
            // Log waktu eksekusi
            $executionTime = round((microtime(true) - $startTime) * 1000);
            \Log::info("[TASK] Request index selesai dalam {$executionTime}ms - ID: $requestId");
            
            return $response;
        } catch (\Exception $e) {
            \Log::error("[TASK] Error saat memproses request index: " . $e->getMessage() . " - ID: $requestId");
            \Log::error("[TASK] " . $e->getTraceAsString());
            
            // Fallback with error notification
            return Inertia::render('Tasks/Index', [
                'tasks' => [],
                'categories' => Category::where('type', 'task')->where('is_active', true)->get(),
                'highlight' => $request->query('highlight'),
                'statusFilter' => $request->query('status'),
                'timestamp' => time(),
                'error' => "Gagal memuat data. Silakan refresh halaman."
            ]);
        }
    }

    public function create()
    {
        return Inertia::render('Tasks/Create', [
            'categories' => Category::where('type', 'task')->where('is_active', true)->get(),
            'platforms' => Platform::where('is_active', true)->get(),
            'teams' => Team::where('is_active', true)->get(),
            'users' => User::where('status', 'active')->get()
        ]);
    }

    public function store(Request $request)
    {
        $requestId = uniqid('create_', true);
        \Log::info("[TASK] Permintaan membuat task baru - ID: $requestId, User: " . auth()->id());
        
        try {
            // 1. Validasi input
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'category_id' => 'nullable|exists:categories,id',
                'platform_id' => 'nullable|exists:platforms,id',
                'team_id' => 'nullable|exists:teams,id',
                'priority' => 'required|in:low,medium,high,urgent',
                'status' => 'required|in:draft,in_progress,completed,cancelled',
                'start_date' => 'nullable|date',
                'due_date' => 'nullable|date',
                'assignee_ids' => 'nullable|array',
                'assignee_ids.*' => 'exists:users,id',
            ]);
            
            // 2. Cek duplikasi (jika task dengan judul sama dibuat oleh user yang sama dalam 30 detik terakhir)
            $existingTask = \App\Models\Task::where('title', $validated['title'])
                ->where('created_by', auth()->id())
                ->where('created_at', '>=', now()->subSeconds(30))
                ->first();
                
            if ($existingTask) {
                \Log::warning("[TASK] Mencegah duplikasi task: '{$validated['title']}' - ID: $requestId");
                
                // Arahkan ke task yang sudah ada
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'message' => 'Task sudah dibuat sebelumnya.',
                        'task_id' => $existingTask->id,
                        'redirect' => route('tasks.index', ['highlight' => $existingTask->id])
                    ], 200);
                }
                
                return redirect()->route('tasks.index', ['highlight' => $existingTask->id])
                    ->with('warning', 'Task dengan judul ini sudah dibuat sebelumnya.');
            }
            
            // 3. Gunakan transaksi database untuk memastikan atomisitas
            $task = DB::transaction(function () use ($validated, $request, $requestId) {
                // Buat task
                $task = new \App\Models\Task();
                $task->title = $validated['title'];
                $task->description = $validated['description'] ?? null;
                $task->category_id = $validated['category_id'] ?? null;
                $task->platform_id = $validated['platform_id'] ?? null;
                $task->team_id = $validated['team_id'] ?? null;
                $task->priority = $validated['priority'];
                $task->status = $validated['status'];
                $task->start_date = $validated['start_date'] ?? null;
                $task->due_date = $validated['due_date'] ?? null;
                $task->created_by = auth()->id();
                
                $task->save();
                
                \Log::info("[TASK] Task berhasil dibuat: ID={$task->id}, Judul='{$task->title}' - ID Request: $requestId");
                
                // Tambahkan assignees jika ada
                if (isset($validated['assignee_ids']) && is_array($validated['assignee_ids'])) {
                    $task->assignees()->sync($validated['assignee_ids']);
                    \Log::debug("[TASK] Ditugaskan ke " . count($validated['assignee_ids']) . " user - ID Request: $requestId");
                }
                
                return $task;
            });
            
            // 4. Bersihkan cache
            \Cache::forget('tasks.all');
            \Cache::forget('tasks.collection');
            
            // Force flush cache lain yang mungkin terkait dengan task
            $this->cleanTaskCache();
            
            // 5. Redirect dengan respons yang sesuai
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Task berhasil dibuat.',
                    'task_id' => $task->id,
                    'redirect' => route('tasks.index', ['highlight' => $task->id])
                ], 201);
            }
            
            return redirect()->route('tasks.index', ['highlight' => $task->id])
                ->with('success', 'Task berhasil dibuat.');
        }
        catch (\Exception $e) {
            \Log::error("[TASK] Gagal membuat task: " . $e->getMessage() . " - ID Request: $requestId");
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Gagal membuat task. Silakan coba lagi.',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withInput()
                ->with('error', 'Gagal membuat task. Silakan coba lagi.');
        }
    }

    public function show($id)
    {
        $task = $this->taskService->getTaskById($id, ['category', 'platform', 'team', 'assignees', 'creator', 'comments.user']);

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
                'team' => $task->team ? [
                    'id' => $task->team->id,
                    'name' => $task->team->name
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

    public function edit($id)
    {
        $task = $this->taskService->getTaskById($id, ['category', 'platform', 'team', 'assignees']);

        return Inertia::render('Tasks/Edit', [
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'category_id' => $task->category_id,
                'platform_id' => $task->platform_id,
                'team_id' => $task->team_id,
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
            'teams' => Team::where('is_active', true)->get(),
            'users' => User::where('status', 'active')->get()
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'platform_id' => 'nullable|exists:platforms,id',
            'team_id' => 'nullable|exists:teams,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:draft,in_progress,completed,cancelled',
            'start_date' => 'nullable|date',
            'due_date' => 'required|date',
            'assignees' => 'required|array|min:1',
            'assignees.*' => 'exists:users,id'
        ]);

        $taskData = [
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'platform_id' => $request->platform_id,
            'team_id' => $request->team_id,
            'priority' => $request->priority,
            'status' => $request->status,
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
            'completed_at' => $request->status === 'completed' ? now() : null,
            'assignees' => $request->assignees
        ];

        $this->taskService->updateTask($id, $taskData);

        return redirect()->route('tasks.index')
            ->with('message', 'Task berhasil diperbarui');
    }

    public function destroy($id)
    {
        $task = $this->taskService->findById($id);
        $this->authorize('delete', $task);
        
        $this->taskService->deleteTask($id);
        $this->cleanTaskCache();
        
        return redirect()->route('tasks.index')->with('success', 'Task berhasil dihapus.');
    }

    /**
     * Update status task via PATCH request
     */
    public function updateStatus(Request $request, $id)
    {
        \Log::info("[TASK] Permintaan update status task ID: $id");
        
        try {
            // Validasi input
            $validated = $request->validate([
                'status' => 'required|in:draft,in_progress,completed,cancelled',
            ]);
            
            // Ambil task dari database
            $task = \App\Models\Task::with(['category', 'platform', 'team', 'assignees', 'creator'])->find($id);
            
            if (!$task) {
                \Log::warning("[TASK] Task dengan ID $id tidak ditemukan untuk update status");
                return response()->json([
                    'success' => false,
                    'message' => 'Task tidak ditemukan'
                ], 404);
            }
            
            // Verifikasi permission
            if (!auth()->user()->can('update', $task)) {
                \Log::warning("[TASK] User " . auth()->id() . " mencoba update status task $id tanpa permission");
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk mengubah status task ini'
                ], 403);
            }
            
            // Update status
            $oldStatus = $task->status;
            $task->status = $validated['status'];
            
            // Jika status berubah menjadi completed, set completed_at
            if ($task->status === 'completed' && $oldStatus !== 'completed') {
                $task->completed_at = now();
            } elseif ($task->status !== 'completed') {
                $task->completed_at = null;
            }
            
            // Simpan perubahan
            $task->save();
            
            // Bersihkan cache
            $this->cleanTaskCache();
            
            \Log::info("[TASK] Status task ID $id berhasil diubah dari $oldStatus menjadi {$task->status}");
            
            // Format response
            return response()->json([
                'success' => true,
                'message' => 'Status task berhasil diperbarui',
                'task' => [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'start_date' => $task->start_date?->format('Y-m-d\TH:i'),
                    'due_date' => $task->due_date?->format('Y-m-d\TH:i'),
                    'completed_at' => $task->completed_at?->format('Y-m-d\TH:i'),
                    'category' => $task->category ? [
                        'id' => $task->category->id,
                        'name' => $task->category->name,
                        'color' => $task->category->color
                    ] : null,
                    'platform' => $task->platform ? [
                        'id' => $task->platform->id,
                        'name' => $task->platform->name,
                        'icon' => $task->platform->icon
                    ] : null,
                    'team' => $task->team ? [
                        'id' => $task->team->id,
                        'name' => $task->team->name
                    ] : null,
                    'assignees' => $task->assignees->map(fn ($user) => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'avatar_url' => $user->avatar_url
                    ])->toArray(),
                    'creator' => $task->creator ? [
                        'id' => $task->creator->id,
                        'name' => $task->creator->name
                    ] : null,
                    'created_at' => $task->created_at->format('d M Y H:i')
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error("[TASK] Error saat update status task ID $id: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status task: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper untuk membersihkan semua cache terkait task
     */
    private function cleanTaskCache()
    {
        try {
            // Hapus cache dengan query database
            \DB::table('cache')->where('key', 'LIKE', '%task%')->delete();
            
            // Clear cache dengan metode Cache facade
            \Cache::flush();
            
            \Log::debug("Task cache dibersihkan");
        } catch (\Exception $e) {
            \Log::error("Gagal membersihkan cache: " . $e->getMessage());
        }
    }
} 