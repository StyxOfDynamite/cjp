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

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <h1 class="text-3xl font-bold text-gray-900 mb-4">About {{ config('seo.business_name') }}</h1>
        <p class="text-gray-600 text-lg mb-10">
            We're a dedicated motorcycle dealership offering quality new and used bikes.
            Whether you're a seasoned rider or just getting started, our team is here to help you find the perfect ride.
        </p>

        <div class="bg-gray-50 border-l-4 border-indigo-500 rounded-r-xl px-6 py-5 mb-10 text-gray-700 leading-relaxed space-y-2">
            <p>For the last 30 years I have been working at Ramoto Motorcycles in Bournemouth.</p>
            <p>Due to their closure I have started up on a smaller scale on my own.</p>
            <p>I will be supplying high quality low mileage motorcycles &amp; scooters.</p>
            <p>With my trade contacts I should be able to source most bikes &mdash; so give me a call and I'll try to find what you are looking for.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-12">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">Visit Us</h2>
                <address class="not-italic text-gray-600 leading-7">
                    {{ config('seo.address_street') }}<br>
                    {{ config('seo.address_city') }}, {{ config('seo.address_region') }} {{ config('seo.address_postcode') }}
                </address>

                <h2 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Opening Hours</h2>
                <ul class="text-gray-600 space-y-1">
                    @foreach(config('seo.opening_hours_display', []) as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">Get in Touch</h2>
                <ul class="text-gray-600 space-y-2">
                    <li>
                        <span class="font-medium">Phone:</span>
                        <a href="tel:{{ config('seo.phone') }}" class="text-indigo-600 hover:underline ml-1">{{ config('seo.phone') }}</a>
                    </li>
                    <li>
                        <span class="font-medium">Email:</span>
                        <a href="mailto:{{ config('seo.email') }}" class="text-indigo-600 hover:underline ml-1">{{ config('seo.email') }}</a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Map --}}
        <div class="rounded-xl overflow-hidden shadow-sm border border-gray-200 aspect-video">
            <iframe
                title="Map showing {{ config('seo.business_name') }} location"
                width="100%"
                height="100%"
                style="border:0"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                src="https://maps.google.com/maps?q={{ urlencode($address) }}&output=embed">
            </iframe>
        </div>

    </div>
</x-app-layout>
