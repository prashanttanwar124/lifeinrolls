<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SupportController extends Controller
{
    public function index(): Response
    {
        $requests = SupportRequest::with('user')->latest()->paginate(10);

        return Inertia::render('admin/Support/Index', [
            'tickets' => $requests,
        ]);
    }

    public function reply(Request $request, SupportRequest $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'admin_response' => 'required|string',
        ]);

        $ticket->update([
            'admin_response' => $validated['admin_response'],
            'status' => 'closed',
        ]);

        return back()->with('flash', [
            'toast' => [
                'type' => 'success',
                'message' => 'Support request response sent.',
            ],
        ]);
    }
}
