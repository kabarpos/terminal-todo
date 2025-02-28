<?php

namespace App\Http\Controllers;

use App\Models\EditorialCalendar;
use App\Models\Platform;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EditorialCalendarController extends Controller
{
    public function index()
    {
        $events = EditorialCalendar::with(['platform', 'category', 'creator', 'assignees'])
            ->orderBy('publish_date')
            ->get()
            ->map(fn ($event) => [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'platform' => [
                    'id' => $event->platform->id,
                    'name' => $event->platform->name,
                    'icon' => $event->platform->icon
                ],
                'category' => [
                    'id' => $event->category->id,
                    'name' => $event->category->name,
                    'color' => $event->category->color
                ],
                'publish_date' => $event->publish_date->format('Y-m-d\TH:i:s'),
                'deadline' => $event->deadline?->format('Y-m-d\TH:i:s'),
                'status' => $event->status,
                'creator' => [
                    'id' => $event->creator->id,
                    'name' => $event->creator->name
                ],
                'assignees' => $event->assignees->map(fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar_url' => $user->avatar_url,
                    'role' => $user->pivot->role
                ]),
                'metadata' => $event->metadata,
                'created_at' => $event->created_at->format('Y-m-d\TH:i:s')
            ]);

        return Inertia::render('Calendar/Index', [
            'events' => $events
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('Calendar/Create', [
            'platforms' => Platform::where('is_active', true)->get(),
            'categories' => Category::where('type', 'content')->where('is_active', true)->get(),
            'users' => User::where('status', 'active')->get(),
            'prefilledDate' => $request->date ?? null
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'platform_id' => 'required|exists:platforms,id',
            'category_id' => 'required|exists:categories,id',
            'publish_date' => 'required|date',
            'deadline' => 'nullable|date|before_or_equal:publish_date',
            'status' => 'required|in:planned,in_progress,published,cancelled',
            'assignees' => 'required|array|min:1',
            'assignees.*' => 'exists:users,id',
            'metadata' => 'nullable|array'
        ]);

        $event = EditorialCalendar::create([
            'title' => $request->title,
            'description' => $request->description,
            'platform_id' => $request->platform_id,
            'category_id' => $request->category_id,
            'publish_date' => $request->publish_date,
            'deadline' => $request->deadline,
            'status' => $request->status,
            'metadata' => $request->metadata,
            'created_by' => Auth::id()
        ]);

        $event->assignees()->attach($request->assignees);

        return redirect()->route('calendar.index')
            ->with('message', 'Event berhasil dibuat');
    }

    public function show(EditorialCalendar $calendar)
    {
        $calendar->load(['platform', 'category', 'creator', 'assignees', 'comments.user']);

        return Inertia::render('Calendar/Show', [
            'event' => [
                'id' => $calendar->id,
                'title' => $calendar->title,
                'description' => $calendar->description,
                'platform' => [
                    'id' => $calendar->platform->id,
                    'name' => $calendar->platform->name,
                    'icon' => $calendar->platform->icon
                ],
                'category' => [
                    'id' => $calendar->category->id,
                    'name' => $calendar->category->name,
                    'color' => $calendar->category->color
                ],
                'publish_date' => $calendar->publish_date->format('Y-m-d\TH:i:s'),
                'deadline' => $calendar->deadline?->format('Y-m-d\TH:i:s'),
                'status' => $calendar->status,
                'creator' => [
                    'id' => $calendar->creator->id,
                    'name' => $calendar->creator->name
                ],
                'assignees' => $calendar->assignees->map(fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar_url' => $user->avatar_url,
                    'role' => $user->pivot->role
                ]),
                'comments' => $calendar->comments->map(fn ($comment) => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user_id' => $comment->user_id,
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                        'avatar_url' => $comment->user->avatar_url ?? null
                    ],
                    'attachment_path' => $comment->attachment_path,
                    'attachment_type' => $comment->attachment_type,
                    'attachment_name' => $comment->attachment_name,
                    'attachment_size' => $comment->attachment_size,
                    'link_url' => $comment->link_url,
                    'created_at' => $comment->created_at->format('Y-m-d\TH:i:s')
                ]),
                'metadata' => $calendar->metadata,
                'created_at' => $calendar->created_at->format('Y-m-d\TH:i:s')
            ]
        ]);
    }

    public function edit(EditorialCalendar $calendar)
    {
        $calendar->load(['platform', 'category', 'assignees']);

        return Inertia::render('Calendar/Edit', [
            'event' => [
                'id' => $calendar->id,
                'title' => $calendar->title,
                'description' => $calendar->description,
                'platform_id' => $calendar->platform_id,
                'category_id' => $calendar->category_id,
                'publish_date' => $calendar->publish_date->format('Y-m-d\TH:i:s'),
                'deadline' => $calendar->deadline?->format('Y-m-d\TH:i:s'),
                'status' => $calendar->status,
                'assignees' => $calendar->assignees->map(function($assignee) {
                    return [
                        'id' => $assignee->id,
                        'name' => $assignee->name
                    ];
                }),
                'metadata' => $calendar->metadata
            ],
            'platforms' => Platform::where('is_active', true)->get(),
            'categories' => Category::where('type', 'content')->where('is_active', true)->get(),
            'users' => User::where('status', 'active')->get()
        ]);
    }

    public function update(Request $request, EditorialCalendar $calendar)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'platform_id' => 'required|exists:platforms,id',
            'category_id' => 'required|exists:categories,id',
            'publish_date' => 'required|date',
            'deadline' => 'nullable|date|before_or_equal:publish_date',
            'status' => 'required|in:planned,in_progress,published,cancelled',
            'assignees' => 'required|array|min:1',
            'assignees.*' => 'exists:users,id',
            'metadata' => 'nullable|array'
        ]);

        $calendar->update([
            'title' => $request->title,
            'description' => $request->description,
            'platform_id' => $request->platform_id,
            'category_id' => $request->category_id,
            'publish_date' => $request->publish_date,
            'deadline' => $request->deadline,
            'status' => $request->status,
            'metadata' => $request->metadata
        ]);

        $calendar->assignees()->sync($request->assignees);

        return redirect()->route('calendar.index')
            ->with('message', 'Event berhasil diperbarui');
    }

    public function destroy(EditorialCalendar $calendar)
    {
        $calendar->delete();

        return redirect()->route('calendar.index')
            ->with('message', 'Event berhasil dihapus');
    }
} 