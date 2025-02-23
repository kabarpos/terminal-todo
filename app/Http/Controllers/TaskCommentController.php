<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskCommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        $comment = $task->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
            'parent_id' => $request->parent_id,
            'attachments' => $request->attachments,
            'mentions' => $request->mentions
        ]);

        $comment->load('user');

        return back()->with('message', 'Komentar berhasil ditambahkan');
    }

    public function update(Request $request, TaskComment $comment)
    {
        $this->authorize('update', $comment);

        $request->validate([
            'content' => 'required|string'
        ]);

        $comment->update([
            'content' => $request->content,
            'attachments' => $request->attachments,
            'mentions' => $request->mentions
        ]);

        return back()->with('message', 'Komentar berhasil diperbarui');
    }

    public function destroy(TaskComment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('message', 'Komentar berhasil dihapus');
    }
} 