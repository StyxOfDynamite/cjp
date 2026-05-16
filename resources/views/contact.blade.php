<x-app-layout>
    @php
        $title = 'Contact Us';
        $description = 'Get in touch with ' . config('seo.business_name') . '. Find our phone number, email, and address.';
    @endphp

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <h1 class="text-3xl font-bold text-gray-900 mb-4">Contact Us</h1>
        <p class="text-gray-600 text-lg mb-10">
            Have a question about a bike, want to arrange a viewing, or just want to say hello?
            Reach us through any of the details below.
        </p>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 divide-y divide-gray-100">

            <div class="flex items-start gap-4 p-6">
                <div class="flex-shrink-0 w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Phone</p>
                    <a href="tel:{{ config('seo.phone') }}" class="text-lg font-medium text-gray-900 hover:text-indigo-600">
                        {{ config('seo.phone') }}
                    </a>
                </div>
            </div>

            <div class="flex items-start gap-4 p-6">
                <div class="flex-shrink-0 w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Email</p>
                    <a href="mailto:{{ config('seo.email') }}" class="text-lg font-medium text-gray-900 hover:text-indigo-600">
                        {{ config('seo.email') }}
                    </a>
                </div>
            </div>

            <div class="flex items-start gap-4 p-6">
                <div class="flex-shrink-0 w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Address</p>
                    <address class="not-italic text-lg font-medium text-gray-900 leading-7">
                        {{ config('seo.address_street') }}<br>
                        {{ config('seo.address_city') }}, {{ config('seo.address_region') }} {{ config('seo.address_postcode') }}
                    </address>
                </div>
            </div>

            <div class="flex items-start gap-4 p-6">
                <div class="flex-shrink-0 w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Opening Hours</p>
                    <ul class="text-gray-700 space-y-0.5">
                        @foreach(config('seo.opening_hours_display', []) as $line)
                            <li>{{ $line }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

        </div>

        {{-- Contact form --}}
        @if(session('success'))
            <div class="mt-8 rounded-xl bg-green-50 border border-green-200 px-5 py-4 text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-10">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Send Us a Message</h2>

            <form method="POST" action="{{ route('contact.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autocomplete="name"
                               class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-400 @enderror">
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                               class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-400 @enderror">
                        @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" autocomplete="tel"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('phone') border-red-400 @enderror">
                    @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message <span class="text-red-500">*</span></label>
                    <textarea id="message" name="message" rows="5" required
                              class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('message') border-red-400 @enderror">{{ old('message') }}</textarea>
                    @error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                    @error('captcha')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    @error('cf-turnstile-response')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <button type="submit"
                            class="bg-indigo-600 text-white text-sm font-medium px-6 py-2.5 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@endpush
</x-app-layout>
