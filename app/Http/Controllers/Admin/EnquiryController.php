<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EnquiryController extends Controller
{
    public function index(): View
    {
        $enquiries = Enquiry::orderByRaw('read_at IS NOT NULL')->orderByDesc('created_at')->paginate(20);

        return view('admin.enquiries.index', compact('enquiries'));
    }

    public function show(Enquiry $enquiry): View
    {
        $enquiry->markAsRead();

        return view('admin.enquiries.show', compact('enquiry'));
    }

    public function markRead(Enquiry $enquiry): RedirectResponse
    {
        $enquiry->markAsRead();

        return back()->with('success', 'Marked as read.');
    }

    public function markUnread(Enquiry $enquiry): RedirectResponse
    {
        $enquiry->update(['read_at' => null]);

        return back()->with('success', 'Marked as unread.');
    }
}
