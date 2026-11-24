<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Messages/Index', [
            'messages' => ContactMessage::latest()->get()->map(fn (ContactMessage $m) => [
                'id' => $m->id, 'name' => $m->name, 'email' => $m->email, 'subject' => $m->subject,
                'excerpt' => str($m->message)->limit(90)->toString(),
                'date' => $m->created_at->format('d/m/Y H:i'), 'read' => $m->read_at !== null,
            ]),
        ]);
    }

    public function show(ContactMessage $message): Response
    {
        $message->read_at ??= now();
        $message->save();

        return Inertia::render('Admin/Messages/Show', [
            'message' => [
                'id' => $message->id, 'name' => $message->name, 'email' => $message->email,
                'subject' => $message->subject, 'message' => $message->message, 'date' => $message->created_at->format('d/m/Y H:i'),
            ],
        ]);
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return redirect()->route('admin.messages.index')->with('success', 'Message supprimé.');
    }
}
