<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhotoReport;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PhotoReportController extends Controller
{
    public function index(): Response
    {
        $reports = PhotoReport::with(['photo.user', 'photo.filmRoll', 'user'])
            ->latest()
            ->paginate(10);

        return Inertia::render('admin/Reports/Index', [
            'reports' => $reports,
        ]);
    }

    public function dismiss(PhotoReport $report): RedirectResponse
    {
        $report->update(['status' => 'dismissed']);

        return back()->with('flash', [
            'toast' => [
                'type' => 'info',
                'message' => 'Photo report dismissed.',
            ],
        ]);
    }

    public function deletePhoto(PhotoReport $report): RedirectResponse
    {
        $photo = $report->photo;
        if ($photo) {
            $photo->delete();
        }

        $report->update(['status' => 'reviewed']);

        return back()->with('flash', [
            'toast' => [
                'type' => 'success',
                'message' => 'Reported photo deleted.',
            ],
        ]);
    }
}
