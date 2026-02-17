<?php

/*
 * This file is part of the Laravel Cloudinary package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    'api_key' => env('CLOUDINARY_API_KEY'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),
    'secure' => true,


     'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | An HTTP or HTTPS URL to notify your application (a webhook) when the process of uploads, deletes, and any API
    | that accepts notification_url has completed.
    |
    |
    */
    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Cloudinary settings. Cloudinary is a cloud hosted
    | media management service for all file uploads, storage, delivery and transformation needs.
    |
    |
    */
    'cloud_url' => env('CLOUDINARY_URL'),

    // Video processing settings
    'video' => [
        'max_file_size' => 1073741824, // 1GB in bytes
        'allowed_formats' => ['mp4', 'mov', 'avi', 'wmv', 'flv', 'webm', 'mpeg'],
        
        // Quality presets based on network strength
        'quality_presets' => [
            'high' => [
                'width' => 1080,
                'height' => 1920,
                'bit_rate' => '3000k',
                'quality' => 'auto:best',
            ],
            'medium' => [
                'width' => 720,
                'height' => 1280,
                'bit_rate' => '1500k',
                'quality' => 'auto:good',
            ],
            'low' => [
                'width' => 480,
                'height' => 854,
                'bit_rate' => '500k',
                'quality' => 'auto:low',
            ],
        ],
    ],


     // Image processing settings
    'image' => [
        'max_file_size' => 104857600, // 100MB in bytes
        'allowed_formats' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        
        // Quality presets
        'quality_presets' => [
            'high' => 'auto:best',
            'medium' => 'auto:good',
            'low' => 'auto:low',
        ],
    ],

    /**
     * Upload Preset From Cloudinary Dashboard
     */
    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),

    /**
     * Route to get cloud_image_url from Blade Upload Widget
     */
    'upload_route' => env('CLOUDINARY_UPLOAD_ROUTE'),

    /**
     * Controller action to get cloud_image_url from Blade Upload Widget
     */
    'upload_action' => env('CLOUDINARY_UPLOAD_ACTION'),
];
