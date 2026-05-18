<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function edit(): View
    {
        $content = Setting::get('about', [
            'tagline' => '30 years in the trade.',
            'body'    => "For the last 30 years I have been working at Ramoto Motorcycles in Bournemouth.\n\nDue to their closure I have started up on a smaller scale on my own.\n\nI will be supplying high quality low mileage motorcycles & scooters.\n\nWith my trade contacts I should be able to source most bikes — so give me a call and I'll try to find what you are looking for.",
        ]);

        return view('admin.about.edit', compact('content'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tagline' => 'required|string|max:255',
            'body'    => 'required|string',
        ]);

        Setting::set('about', $data);

        return back()->with('success', 'About page updated.');
    }
}
