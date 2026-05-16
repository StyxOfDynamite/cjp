<?php

return [
    'business_name'       => env('SEO_BUSINESS_NAME', 'CJP Motorcycles'),
    'phone'               => env('SEO_PHONE', '+61 2 0000 0000'),
    'email'               => env('SEO_EMAIL', 'info@cjpmotorcycles.com.au'),
    'address_street'      => env('SEO_ADDRESS_STREET', '1 Example Street'),
    'address_city'        => env('SEO_ADDRESS_CITY', 'Sydney'),
    'address_region'      => env('SEO_ADDRESS_REGION', 'NSW'),
    'address_postcode'    => env('SEO_ADDRESS_POSTCODE', '2000'),
    'address_country'     => env('SEO_ADDRESS_COUNTRY', 'AU'),
    'default_description' => env('SEO_DEFAULT_DESCRIPTION', 'Quality motorcycles for sale. Browse our full range of new and used bikes.'),
    'opening_hours'         => ['Mo-Fr 09:00-15:00'],
    'opening_hours_display' => ['Monday – Friday: 9am – 3pm', 'Saturday: Closed', 'Sunday: Closed'],

    'logo_url'        => env('SEO_LOGO_URL'),
    'geo_latitude'    => env('SEO_GEO_LATITUDE'),
    'geo_longitude'   => env('SEO_GEO_LONGITUDE'),
    'social_facebook' => env('SEO_FACEBOOK_URL'),
    'social_instagram'=> env('SEO_INSTAGRAM_URL'),
];
