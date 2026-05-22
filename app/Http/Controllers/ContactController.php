<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ContactController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'                   => 'required|string|max:100',
            'email'                  => 'required|email|max:255',
            'phone'                  => 'nullable|string|max:30',
            'message'                => 'required|string|max:3000',
            'cf-turnstile-response'  => 'required|string',
        ]);

        $verify = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret'   => config('services.turnstile.secret_key'),
            'response' => $request->input('cf-turnstile-response'),
            'remoteip' => $request->ip(),
        ]);

        if (! $verify->json('success')) {
            return back()
                ->withErrors(['captcha' => 'CAPTCHA verification failed — please try again.'])
                ->withInput();
        }

        Enquiry::create([
            'name'    => $data['name'],
            'email'   => $data['email'],
            'phone'   => $data['phone'] ?? null,
            'message' => $data['message'],
        ]);

        return redirect()->route('contact')->with('success', "Thanks for getting in touch! We'll get back to you soon.");
    }
}
