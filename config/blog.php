<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin panel
    |--------------------------------------------------------------------------
    | Single shared password gate for /admin. Set ADMIN_PASSWORD in .env.
    */
    'admin_password' => env('ADMIN_PASSWORD', 'change-me'),

    /*
    |--------------------------------------------------------------------------
    | Featured / inline images (keyless, AI-generated)
    |--------------------------------------------------------------------------
    | Pollinations generates an image from a text prompt with no API key.
    | {prompt} is URL-encoded at render time.
    */
    'images' => [
        'enabled' => env('BLOG_IMAGES_ENABLED', true),
        'endpoint' => env('BLOG_IMAGE_ENDPOINT', 'https://image.pollinations.ai/prompt/'),
        'width' => 1200,
        'height' => 630,
        'extra' => 'nologo=true&enhance=true',
    ],

    /*
    |--------------------------------------------------------------------------
    | Google AdSense (auto income)
    |--------------------------------------------------------------------------
    | Leave client empty to show labelled "Advertisement" placeholders that
    | still reserve the correct space. Fill it in to serve live ads.
    */
    'adsense' => [
        'client' => env('ADSENSE_CLIENT', ''),          // ca-pub-XXXXXXXXXXXXXXXX
        'slots' => [
            'leaderboard' => env('ADSENSE_SLOT_LEADERBOARD', ''),
            'in_article' => env('ADSENSE_SLOT_IN_ARTICLE', ''),
            'sidebar' => env('ADSENSE_SLOT_SIDEBAR', ''),
        ],
        // Insert an in-article ad after the intro and after every Nth H2.
        'in_article_every' => 2,
    ],

    /*
    |--------------------------------------------------------------------------
    | Reading time
    |--------------------------------------------------------------------------
    */
    'words_per_minute' => 200,
];
