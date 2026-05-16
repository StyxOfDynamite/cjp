<x-app-layout>
    @php
        $title = 'About Us';
        $description = 'Learn more about ' . config('seo.business_name') . ' — your local motorcycle dealership.';
        $canonical = route('about');
        $address = implode(', ', array_filter([
            config('seo.address_street'),
            config('seo.address_city'),
            config('seo.address_region'),
            config('seo.address_postcode'),
        ]));

        $socialLinks = array_values(array_filter([
            config('seo.social_facebook'),
            config('seo.social_instagram'),
        ]));

        $jsonLd = array_filter([
            '@context' => 'https://schema.org',
            '@type'    => 'MotorcycleDealer',
            'name'     => config('seo.business_name'),
            'url'      => config('app.url'),
            'telephone'=> config('seo.phone'),
            'email'    => config('seo.email'),
            'logo'     => config('seo.logo_url') ? ['@type' => 'ImageObject', 'url' => config('seo.logo_url')] : null,
            'address'  => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => config('seo.address_street'),
                'addressLocality' => config('seo.address_city'),
                'addressRegion'   => config('seo.address_region'),
                'postalCode'      => config('seo.address_postcode'),
                'addressCountry'  => config('seo.address_country'),
            ],
            'geo' => (config('seo.geo_latitude') && config('seo.geo_longitude')) ? [
                '@type'    => 'GeoCoordinates',
                'latitude' => config('seo.geo_latitude'),
                'longitude'=> config('seo.geo_longitude'),
            ] : null,
            'openingHours' => config('seo.opening_hours') ?: null,
            'sameAs'       => $socialLinks ?: null,
        ]);
    @endphp

    {{-- Hero --}}
    <div class="border-b border-white/10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <p class="text-xs font-medium uppercase tracking-widest text-amber-400 mb-4">About</p>
            <h1 class="text-5xl sm:text-6xl font-bold tracking-tight text-white leading-none">
                {{ config('seo.business_name') }}
            </h1>
            <p class="mt-4 text-zinc-400 text-lg">
                {{ config('seo.address_city') }} &mdash; Quality Motorcycles
            </p>
        </div>
    </div>

    {{-- Story --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="w-12 h-px bg-amber-500 mb-10"></div>
        <blockquote class="text-4xl sm:text-5xl font-bold text-white tracking-tight leading-tight mb-12 max-w-3xl">
            30 years in the trade.
        </blockquote>
        <div class="max-w-2xl space-y-5 text-zinc-300 text-lg leading-relaxed">
            <p>For the last 30 years I have been working at Ramoto Motorcycles in Bournemouth.</p>
            <p>Due to their closure I have started up on a smaller scale on my own.</p>
            <p>I will be supplying high quality low mileage motorcycles &amp; scooters.</p>
            <p>With my trade contacts I should be able to source most bikes &mdash; so give me a call and I'll try to find what you are looking for.</p>
        </div>
    </div>

    {{-- Info strip --}}
    <div class="border-t border-white/10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid grid-cols-1 md:grid-cols-3 gap-12">
            <div>
                <p class="text-xs font-medium uppercase tracking-widest text-zinc-500 mb-3">Find Us</p>
                <address class="not-italic text-zinc-300 leading-7">
                    {{ config('seo.address_street') }}<br>
                    {{ config('seo.address_city') }}<br>
                    {{ config('seo.address_region') }} {{ config('seo.address_postcode') }}
                </address>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-widest text-zinc-500 mb-3">Opening Hours</p>
                <ul class="text-zinc-300 space-y-1 leading-7">
                    @foreach(config('seo.opening_hours_display', []) as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ul>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-widest text-zinc-500 mb-3">Get in Touch</p>
                <div class="space-y-2">
                    <div>
                        <a href="tel:{{ config('seo.phone') }}" class="text-2xl font-bold text-amber-400 hover:text-amber-300 transition-colors tracking-tight">
                            {{ config('seo.phone') }}
                        </a>
                    </div>
                    <div>
                        <a href="mailto:{{ config('seo.email') }}" class="text-zinc-300 hover:text-amber-400 transition-colors">
                            {{ config('seo.email') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Map --}}
    <div class="border-t border-white/10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="aspect-video border border-white/10 overflow-hidden">
                <iframe
                    title="Map showing {{ config('seo.business_name') }} location"
                    width="100%"
                    height="100%"
                    style="border:0;filter:invert(90%) hue-rotate(180deg)"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    src="https://maps.google.com/maps?q={{ urlencode($address) }}&output=embed">
                </iframe>
            </div>
        </div>
    </div>

</x-app-layout>
