{{-- SEO partial: pass $title, $description, $canonical, $image, $type, $jsonLd --}}
<title>{{ isset($title) ? $title . ' | ' : '' }}{{ config('seo.business_name') }}</title>
<meta name="description" content="{{ $description ?? config('seo.default_description', '') }}">
<link rel="canonical" href="{{ $canonical ?? url()->current() }}">

<meta property="og:title" content="{{ $title ?? config('seo.business_name') }}">
<meta property="og:description" content="{{ $description ?? config('seo.default_description', '') }}">
<meta property="og:url" content="{{ $canonical ?? url()->current() }}">
<meta property="og:type" content="{{ $type ?? 'website' }}">
@isset($image)
<meta property="og:image" content="{{ $image }}">
<meta name="twitter:image" content="{{ $image }}">
@endisset
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title ?? config('seo.business_name') }}">
<meta name="twitter:description" content="{{ $description ?? config('seo.default_description', '') }}">

@isset($jsonLd)
<script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
@endisset

{{-- LocalBusiness JSON-LD on every page --}}
<script type="application/ld+json">{!! json_encode([
    '@@context' => 'https://schema.org',
    '@type' => 'MotorcycleDealer',
    'name' => config('seo.business_name'),
    'url' => config('app.url'),
    'telephone' => config('seo.phone'),
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => config('seo.address_street'),
        'addressLocality' => config('seo.address_city'),
        'addressRegion' => config('seo.address_region'),
        'postalCode' => config('seo.address_postcode'),
        'addressCountry' => config('seo.address_country'),
    ],
    'openingHours' => config('seo.opening_hours', []),
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
