<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Upload limits (kilobytes)
    |--------------------------------------------------------------------------
    */
    'video_max_kb' => [
        'Creator'    => (int) (env('VIDEO_MAX_KB_CREATOR') ?: 1048576),
        'Influencer' => (int) (env('VIDEO_MAX_KB_INFLUENCER') ?: 1048576),
    ],

    'video_max_seconds' => [
        'Creator'    => (int) (env('VIDEO_MAX_SECONDS_CREATOR') ?: 0),
        'Influencer' => (int) (env('VIDEO_MAX_SECONDS_INFLUENCER') ?: 600),
    ],

    'image_max_kb' => (int) (env('IMAGE_MAX_KB') ?: 10240),

    /*
    |--------------------------------------------------------------------------
    | TikTok-style H.264 renditions (MP4 + faststart for progressive playback)
    |--------------------------------------------------------------------------
    */
    'video_variants' => [
        'high' => [
            'file'   => 'high.mp4',
            'width'  => 1080,
            'crf'    => '23',
            'preset' => 'medium',
            'audio'  => '128k',
        ],
        'medium' => [
            'file'   => 'medium.mp4',
            'width'  => 720,
            'crf'    => '25',
            'preset' => 'fast',
            'audio'  => '96k',
        ],
        'low' => [
            'file'   => 'low.mp4',
            'width'  => 480,
            'crf'    => '28',
            'preset' => 'veryfast',
            'audio'  => '64k',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image processing (WebP output)
    |--------------------------------------------------------------------------
    */
    'image' => [
        'format'     => 'webp',
        'quality'    => 82,
        'max_width'  => 2048,
        'max_height' => 2048,
    ],

    'staging_disk' => 'local',
    'staging_path' => 'video-staging',

    'upload_cache_ttl' => 3600, // seconds — poll result cache

];
