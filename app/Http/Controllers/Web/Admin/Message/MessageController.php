<?php

namespace App\Http\Controllers\Web\Admin\Message;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $query = Message::with('sender', 'receiver')
            ->where(function ($q) {
                $q->where('sender_id', Auth::id())
                  ->orWhere('receiver_id', Auth::id());
            });

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhereHas('sender', function ($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('receiver', function ($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('type')) {
            if ($request->type === 'sent') {
                $query->where('sender_id', Auth::id());
            } elseif ($request->type === 'received') {
                $query->where('receiver_id', Auth::id());
            }
        }

        if ($request->has('is_read')) {
            $query->where('is_read', $request->is_read === '1');
        }

        $messages = $query->latest()->paginate(15);

        return Inertia::render('Admin/Message/Index', [
            'messages' => $messages,
            'filters' => $request->only(['search', 'type', 'is_read']),
        ]);
    }

    public function create(Request $request)
    {
        $receiverId = $request->receiver_id;

        $users = User::where('id', '!=', Auth::id())
            ->whereIn('type', ['admin', 'teacher', 'student', 'parent'])
            ->get(['id', 'first_name', 'last_name', 'email', 'type']);

        $receiver = $receiverId ? User::find($receiverId) : null;

        return Inertia::render('Admin/Message/Create', [
            'users' => $users,
            'receiver' => $receiver,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'priority' => $validated['priority'],
            'is_read' => false,
        ]);

        return redirect()->route('admin.messages.index')
            ->with('success', 'Message sent successfully.');
    }

    public function show($id)
    {
        $message = Message::with('sender', 'receiver')
            ->where(function ($q) {
                $q->where('sender_id', Auth::id())
                  ->orWhere('receiver_id', Auth::id());
            })
            ->findOrFail($id);

        // Mark as read if receiver is current user
        if ($message->receiver_id === Auth::id() && !$message->is_read) {
            $message->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return Inertia::render('Admin/Message/Show', [
            'message' => $message,
        ]);
    }

    public function destroy($id)
    {
        $message = Message::where(function ($q) {
            $q->where('sender_id', Auth::id())
              ->orWhere('receiver_id', Auth::id());
        })->findOrFail($id);

        $message->delete();

        return redirect()->route('admin.messages.index')
            ->with('success', 'Message deleted successfully.');
    }

    public function markAsRead($id)
    {
        $message = Message::where('receiver_id', Auth::id())
            ->findOrFail($id);

        $message->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return back()->with('success', 'Message marked as read.');
    }
}
