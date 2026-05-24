<?php

return [
    'business_name' => env('SEO_BUSINESS_NAME', 'CJP Motorcycles'),
    'phone' => env('SEO_PHONE', '+61 2 0000 0000'),
    'email' => env('SEO_EMAIL', 'info@cjpmotorcycles.com.au'),
    'address_street' => env('SEO_ADDRESS_STREET', '1 Example Street'),
    'address_city' => env('SEO_ADDRESS_CITY', 'Sydney'),
    'address_region' => env('SEO_ADDRESS_REGION', 'NSW'),
    'address_postcode' => env('SEO_ADDRESS_POSTCODE', '2000'),
    'address_country' => env('SEO_ADDRESS_COUNTRY', 'AU'),
    'default_description' => "Discover pristine, low-mileage classic motorcycles in Dorset. Handpicked vintage bikes, fully inspected and ready for the road. View our latest stock today.",
    'opening_hours' => [env('SEO_OPENING_HOURS', 'Mo-Fr 09:00-15:00')],
    'opening_hours_display' => ['Monday – Friday: 9am – 3pm', 'Saturday – Sunday: Closed'],

    'logo_url' => env('SEO_LOGO_URL', 'https://fls-a1ca0e0c-a579-4763-8c09-2097012b214f.laravel.cloud/logo-dark.png'),
    'logo_light_url' => env('SEO_LOGO_LIGHT_URL', 'https://fls-a1ca0e0c-a579-4763-8c09-2097012b214f.laravel.cloud/logo-lighter.png'),
    'geo_latitude' => env('SEO_GEO_LATITUDE'),
    'geo_longitude' => env('SEO_GEO_LONGITUDE'),
    'social_facebook' => env('SEO_FACEBOOK_URL'),
    'social_instagram' => env('SEO_INSTAGRAM_URL'),
];
