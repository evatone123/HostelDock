<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Static Slider Images
    |--------------------------------------------------------------------------
    |
    | Fallback images used anywhere a hostel has no uploaded photo (hostel
    | cards, covers, sliders and the detail gallery render the hostel's real
    | uploaded files from hostels_images when they exist; these folder files
    | are the safe default). To add one, drop the file into
    | public/assets/images/about/ and add its path below.
    |
    */

    'slider_images' => [
        'assets/images/about/slider-imgs.jpg',
        'assets/images/about/slider-imgs2.jpg',
    ],

    /*
    |--------------------------------------------------------------------------
    | Asset Version (cache buster)
    |--------------------------------------------------------------------------
    |
    | Logos and slider images render as .../file?v=<this>. Bump it whenever a
    | logo or slider file in public/assets is replaced so browsers fetch the new
    | folder file instead of a cached copy.
    |
    */

    'asset_version' => '20260929a',

];
