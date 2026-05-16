<x-app-layout>
    @php
        $title = 'Contact Us';
        $description = 'Get in touch with ' . config('seo.business_name') . '. Find our phone number, email, and address.';
    @endphp

    {{-- Header --}}
    <div class="border-b border-white/10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <p class="text-xs font-medium uppercase tracking-widest text-amber-400 mb-4">Contact</p>
            <h1 class="text-5xl sm:text-6xl font-bold tracking-tight text-white leading-none">
                Talk to us about<br>your next bike.
            </h1>
            <p class="mt-5 text-zinc-400 text-lg max-w-lg">
                Call, email, or drop us a message. We're happy to help you find the right ride.
            </p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-16">

            {{-- Contact details --}}
            <div class="lg:col-span-2 space-y-10">

                <div>
                    <p class="text-xs font-medium uppercase tracking-widest text-zinc-500 mb-2">Call us</p>
                    <a href="tel:{{ config('seo.phone') }}"
                       class="text-2xl font-bold text-amber-400 hover:text-amber-300 transition-colors tracking-tight">
                        {{ config('seo.phone') }}
                    </a>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-widest text-zinc-500 mb-2">Email</p>
                    <a href="mailto:{{ config('seo.email') }}"
                       class="text-zinc-200 hover:text-amber-400 transition-colors">
                        {{ config('seo.email') }}
                    </a>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-widest text-zinc-500 mb-2">Visit</p>
                    <address class="not-italic text-zinc-300 leading-7">
                        {{ config('seo.address_street') }}<br>
                        {{ config('seo.address_city') }}<br>
                        {{ config('seo.address_region') }} {{ config('seo.address_postcode') }}
                    </address>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-widest text-zinc-500 mb-2">Hours</p>
                    <ul class="text-zinc-300 space-y-1 leading-7">
                        @foreach(config('seo.opening_hours_display', []) as $line)
                            <li>{{ $line }}</li>
                        @endforeach
                    </ul>
                </div>

            </div>

            {{-- Form --}}
            <div class="lg:col-span-3">
                @if(session('success'))
                    <div class="glass p-8">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 bg-amber-500 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                            </div>
                            <p class="text-white font-semibold">Message sent</p>
                        </div>
                        <p class="text-zinc-400 text-sm">{{ session('success') }}</p>
                    </div>
                @else
                    <form method="POST" action="{{ route('contact.store') }}" class="glass p-8 space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="name" class="block text-xs font-medium text-zinc-500 uppercase tracking-wide mb-1.5">
                                    Name <span class="text-red-400">*</span>
                                </label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required autocomplete="name"
                                       class="w-full bg-white/5 border-white/10 text-zinc-100 placeholder-zinc-600 text-sm rounded-none focus:border-amber-500 focus:ring-amber-500 @error('name') border-red-500/50 @enderror">
                                @error('name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="email" class="block text-xs font-medium text-zinc-500 uppercase tracking-wide mb-1.5">
                                    Email <span class="text-red-400">*</span>
                                </label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                                       class="w-full bg-white/5 border-white/10 text-zinc-100 placeholder-zinc-600 text-sm rounded-none focus:border-amber-500 focus:ring-amber-500 @error('email') border-red-500/50 @enderror">
                                @error('email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label for="phone" class="block text-xs font-medium text-zinc-500 uppercase tracking-wide mb-1.5">
                                Phone <span class="text-zinc-600 normal-case tracking-normal font-normal">(optional)</span>
                            </label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" autocomplete="tel"
                                   class="w-full bg-white/5 border-white/10 text-zinc-100 placeholder-zinc-600 text-sm rounded-none focus:border-amber-500 focus:ring-amber-500 @error('phone') border-red-500/50 @enderror">
                            @error('phone')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="message" class="block text-xs font-medium text-zinc-500 uppercase tracking-wide mb-1.5">
                                Message <span class="text-red-400">*</span>
                            </label>
                            <textarea id="message" name="message" rows="5" required
                                      class="w-full bg-white/5 border-white/10 text-zinc-100 placeholder-zinc-600 text-sm rounded-none focus:border-amber-500 focus:ring-amber-500 @error('message') border-red-500/50 @enderror">{{ old('message') }}</textarea>
                            @error('message')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                            @error('captcha')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            @error('cf-turnstile-response')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <button type="submit"
                                    class="bg-amber-500 text-black text-sm font-semibold px-8 py-3 hover:bg-amber-400 transition-colors">
                                Send Message
                            </button>
                        </div>
                    </form>
                @endif
            </div>

        </div>
    </div>

@push('scripts')
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@endpush
</x-app-layout>
