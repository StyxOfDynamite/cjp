{{-- SEO partial: pass $title, $description, $canonical, $image, $imageAlt, $type, $jsonLd --}}
@php
    $seoTagline     = config('seo.business_tagline') . ' | ' . config('seo.business_name');
    $seoTitle       = isset($title) ? $title . ' | ' . $seoTagline : $seoTagline;
    $seoDescription = $description ?? config('seo.default_description', '');
    $seoCanonical   = $canonical ?? url()->current();
    $seoImage       = $image ?? config('seo.logo_url');
    $seoImageAlt    = $imageAlt ?? config('seo.business_name');
    $seoType        = $type ?? 'website';
@endphp

<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<link rel="canonical" href="{{ $seoCanonical }}">

{{-- Open Graph --}}
<meta property="og:site_name" content="{{ config('seo.business_name') }}">
<meta property="og:locale" content="en_GB">
<meta property="og:type" content="{{ $seoType }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoCanonical }}">
@if($seoImage)
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:image:alt" content="{{ $seoImageAlt }}">
@endif

{{-- Twitter / X --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
@if($seoImage)
<meta name="twitter:image" content="{{ $seoImage }}">
<meta name="twitter:image:alt" content="{{ $seoImageAlt }}">
@endif

{{-- Page JSON-LD --}}
@isset($jsonLd)
<script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
@endisset

{{-- LocalBusiness JSON-LD — present on every page --}}
@php
    $localBusiness = array_filter([
        '@@context'   => 'https://schema.org',
        '@type'       => 'MotorcycleDealer',
        'name'        => config('seo.business_name'),
        'url'         => config('app.url'),
        'telephone'   => config('seo.phone'),
        'email'       => config('seo.email') ?: null,
        'logo'        => config('seo.logo_url') ? ['@type' => 'ImageObject', 'url' => config('seo.logo_url')] : null,
        'address'     => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => config('seo.address_street'),
            'addressLocality' => config('seo.address_city'),
            'addressRegion'   => config('seo.address_region'),
            'postalCode'      => config('seo.address_postcode'),
            'addressCountry'  => config('seo.address_country'),
        ],
        'geo' => (config('seo.geo_latitude') && config('seo.geo_longitude')) ? [
            '@type'     => 'GeoCoordinates',
            'latitude'  => config('seo.geo_latitude'),
            'longitude' => config('seo.geo_longitude'),
        ] : null,
        'openingHours' => config('seo.opening_hours') ?: null,
        'sameAs'       => array_values(array_filter([
            config('seo.social_facebook'),
            config('seo.social_instagram'),
        ])) ?: null,
    ]);
@endphp
<script type="application/ld+json">{!! json_encode($localBusiness, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
