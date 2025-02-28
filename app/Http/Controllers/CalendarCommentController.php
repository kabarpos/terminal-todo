<?php

namespace App\Http\Controllers;

use App\Models\EditorialCalendar;
use App\Models\CalendarComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CalendarCommentController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, EditorialCalendar $calendar)
    {
        $request->validate([
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // Max 10MB
            'link_url' => 'nullable|url'
        ]);

        $commentData = [
            'user_id' => Auth::id(),
            'content' => $request->content,
            'link_url' => $request->link_url
        ];

        // Handle file upload if present
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('calendar_attachments', $fileName, 'public');
            
            $commentData['attachment_path'] = $path;
            $commentData['attachment_type'] = $file->getClientMimeType();
            $commentData['attachment_name'] = $file->getClientOriginalName();
            $commentData['attachment_size'] = $file->getSize();
        }

        $comment = $calendar->comments()->create($commentData);

        return back()->with('message', 'Komentar berhasil ditambahkan');
    }

    public function destroy(EditorialCalendar $calendar, CalendarComment $comment)
    {
        $this->authorize('delete', $comment);
        
        // Delete attachment if exists
        if ($comment->attachment_path) {
            Storage::disk('public')->delete($comment->attachment_path);
        }
        
        $comment->delete();

        return back()->with('message', 'Komentar berhasil dihapus');
    }
} 