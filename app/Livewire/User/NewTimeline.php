<?php

namespace App\Livewire\User;

use Livewire\WithFileUploads;
use Livewire\Component;
use App\Models\Post;
use App\Models\PostImages;
use App\Models\PostVideo;
use App\Models\PostVideos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;

class NewTimeline extends Component
{
    use WithFileUploads;

    public $content = '';
    public $images = [];
    public $video;
    public $uploadProgress = 0;
    public $isUploading = false;
    public $videoPreview = null;

    use WithFileUploads;


    public $videoData = null;

    protected $listeners = ['videoUploaded'];

    // protected $listeners = ['refreshComponent' => '$refresh'];

    // public function videoUploaded($data)
    // {
    //     $this->videoData = $data;
    // }

    public function updatedVideo()
    {
        $this->validateOnly('video', [
            'video' => 'nullable|mimes:mp4,mov,avi,webm|max:204800', // 200MB
        ]);

        // Clear images if video selected
        $this->images = [];

        if ($this->video) {
            $this->videoPreview = $this->video->temporaryUrl();
        }
    }


    public function removeImage($index)
    {
        unset($this->images[$index]);
        $this->images = array_values($this->images);
    }

    public function removeVideo()
    {
        $this->reset('video', 'videoPreview');
    }



    public function createPost()
    {
        $level = userLevel();

        $rules = [
            'content' => 'required|string',
        ];

        if (!in_array($level, ['Creator', 'Influencer'])) {
            $rules['content'] .= '|max:160';
            $rules['images'] = 'prohibited';
        } else {
            $rules['images'] = 'nullable|array|max:4';
            $rules['images.*'] = 'image|max:2048';
        }

        $this->validate($rules);

        $user = Auth::user();

        $status = $user->status === 'ACTIVE' ? 'LIVE' : 'SHADOW_BANNED';

        $post = Post::create([
            'user_id' => $user->id,
            'content' => $this->content,
            'unicode' => rand(1000, 9999) . time(),
            'comment_external' => 0,
            'status' => $status,
        ]);

        // Upload Images to Cloudinary
        if (!empty($this->images)) {
            foreach ($this->images as $image) {

                $uploadedFileUrl = cloudinary()->upload(
                    $image->getRealPath(),
                    ['folder' => 'payhankey_post_images']
                )->getSecurePath();

                PostImages::create([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                    'path' => $uploadedFileUrl,
                ]);
            }
            $post->update(['has_images' => 1]);
        }

        if ($this->video) {

            // $this->uploadVideos($post, $user);

            try {

                $uploadResult = cloudinary()->uploadVideo($this->video->getRealPath(), [
                    'folder' => 'payhankey_post_videos',
                    'resource_type' => 'video',
                    'eager' => $this->getVideoTransformations('medium'), // Default to medium for initial upload
                    'eager_async' => true,
                    'eager_notification_url' => route('cloudinary.webhook'),
                ]);

                $response = $uploadResult->getResponse();

                //titkok style thumbnail cropping based on aspect ratio
                $crop = $response['height'] > $response['width']
                    ? 'c_fill,w_720,h_1280'
                    : 'c_fill,w_1280,h_720';

                $thumbnailUrl = str_replace(
                    '/video/upload/',
                    "/video/upload/so_2,{$crop},f_jpg/",
                    $response['secure_url']
                );


                PostVideo::create([
                    'user_id'   => $user->id,
                    'post_id'   => $post->id,
                    'public_id' => $response['public_id'],
                    'path'      => $response['secure_url'],
                    'thumbnail_path' => $thumbnailUrl,
                    'duration'  => $response['duration'] ?? null,
                    'width'     => $response['width'] ?? null,
                    'height'    => $response['height'] ?? null,
                    'format' => $response['format'] ?? null,
                    'file_size' => $this->video->getSize(),
                ]);

                $post->update(['has_video' => 1]);
            } catch (\Exception $e) {

                Log::error('Cloudinary Video Upload Error: ' . $e->getMessage());
            }
        }

        session()->flash('success', 'Your post was successful!');

        $this->reset(['content', 'images', 'video', 'videoPreview', 'videoData']);
    }

    private function uploadVideos($post, $user)
    {
        foreach ($this->video as $video) {
            try {
                $uploadResult = cloudinary()->uploadVideo($video->getRealPath(), [
                    'folder' => 'payhankey_post_videos',
                    'resource_type' => 'video',
                    'eager' => $this->getVideoTransformations('medium'), // Default to medium for initial upload
                    'eager_async' => true,
                    'eager_notification_url' => route('cloudinary.webhook'),
                ]);

                PostVideo::create([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                    'path' => $uploadResult->getSecurePath(),
                    'public_id' => $uploadResult->getPublicId(),
                    'processing_status' => 'processing',
                    'duration' => $uploadResult->getResponse()['duration'] ?? null,
                    'width' => $uploadResult->getResponse()['width'] ?? null,
                    'height' => $uploadResult->getResponse()['height'] ?? null,
                    'format' => $uploadResult->getResponse()['format'] ?? null,
                    'file_size' => $video->getSize(),
                ]);
            } catch (\Exception $e) {
                Log::error('Video upload error: ' . $e->getMessage());
            }
        }
    }

    private function getVideoTransformations($networkStrength)
    {
        // Generate multiple quality versions like TikTok/Instagram
        $transformations = [];

        switch ($networkStrength) {
            case 'slow':
            case '2g':
                // Low quality only for slow networks
                $transformations[] = [
                    'width' => 480,
                    'height' => 854,
                    'crop' => 'limit',
                    'quality' => 'auto:low',
                    'video_codec' => 'h264',
                    'audio_codec' => 'aac',
                    'bit_rate' => '500k',
                ];
                break;

            case '3g':
            case 'medium':
                // Medium and low quality
                $transformations[] = [
                    'width' => 720,
                    'height' => 1280,
                    'crop' => 'limit',
                    'quality' => 'auto:good',
                    'video_codec' => 'h264',
                    'audio_codec' => 'aac',
                    'bit_rate' => '1500k',
                ];
                $transformations[] = [
                    'width' => 480,
                    'height' => 854,
                    'crop' => 'limit',
                    'quality' => 'auto:low',
                    'video_codec' => 'h264',
                    'audio_codec' => 'aac',
                    'bit_rate' => '500k',
                ];
                break;

            case '4g':
            case '5g':
            case 'fast':
            default:
                // High, medium, and low quality (adaptive streaming)
                $transformations[] = [
                    'width' => 1080,
                    'height' => 1920,
                    'crop' => 'limit',
                    'quality' => 'auto:best',
                    'video_codec' => 'h264',
                    'audio_codec' => 'aac',
                    'bit_rate' => '3000k',
                ];
                $transformations[] = [
                    'width' => 720,
                    'height' => 1280,
                    'crop' => 'limit',
                    'quality' => 'auto:good',
                    'video_codec' => 'h264',
                    'audio_codec' => 'aac',
                    'bit_rate' => '1500k',
                ];
                $transformations[] = [
                    'width' => 480,
                    'height' => 854,
                    'crop' => 'limit',
                    'quality' => 'auto:low',
                    'video_codec' => 'h264',
                    'audio_codec' => 'aac',
                    'bit_rate' => '500k',
                ];
                break;
        }

        return $transformations;
    }

    public function render()
    {
        return view('livewire.user.new-timeline');
    }

    // public function updatedImages()
    // {
    //     $level = userLevel();
    //     $maxImages = match ($level) {
    //         'Creator' => 1,
    //         'Influencer' => 4,
    //         default => 0,
    //     };

    //     if (count($this->images) > $maxImages) {
    //         $this->images = array_slice($this->images, 0, $maxImages);
    //         session()->flash('warning', "Maximum {$maxImages} image(s) allowed.");
    //     }
    // }


    // public function updatedVideos()
    // {
    //     $level = userLevel();

    //     // Only Creators and Influencers can upload videos
    //     if (!in_array($level, ['Creator', 'Influencer'])) {
    //         $this->videos = [];
    //         session()->flash('error', 'Only Creators and Influencers can upload videos.');
    //         return;
    //     }

    //     // Limit to 1 video per post (like TikTok)
    //     if (count($this->videos) > 1) {
    //         $this->videos = array_slice($this->videos, 0, 1);
    //         session()->flash('warning', "Only 1 video allowed per post.");
    //     }

    //     // Validate video size (1GB max)
    //     foreach ($this->videos as $video) {
    //         $sizeInMB = $video->getSize() / 1024 / 1024;
    //         if ($sizeInMB > 1024) {
    //             $this->videos = [];
    //             session()->flash('error', 'Video size must not exceed 1GB.');
    //             return;
    //         }
    //     }
    // }

    // public function removeImage($index)
    // {
    //     array_splice($this->images, $index, 1);
    // }

    // public function removeVideo($index)
    // {
    //     array_splice($this->videos, $index, 1);
    // }

    // public function createPost()
    // {
    //     $this->isUploading = true;
    //     $level = userLevel();

    //     // Validation rules
    //     $rules = [
    //         'content' => 'required|string',
    //     ];

    //     if (!in_array($level, ['Creator', 'Influencer'])) {
    //         $rules['content'] .= '|max:160';
    //         $rules['images'] = 'prohibited';
    //         $rules['videos'] = 'prohibited';
    //     } else {
    //         $rules['images'] = 'nullable|array|max:4';
    //         $rules['images.*'] = 'image|max:102400'; // 100MB
    //         $rules['videos'] = 'nullable|array|max:1';
    //         $rules['videos.*'] = 'mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime,video/x-msvideo,video/x-flv,video/webm|max:1048576'; // 1GB
    //     }

    //     $this->validate($rules);



    //     // Content length check
    //     $maxLength = in_array($level, ['Creator', 'Influencer']) ? null : 160;
    //     if ($maxLength && strlen($this->content) > $maxLength) {
    //         session()->flash('error', "You cannot post more than $maxLength characters.");
    //         $this->isUploading = false;
    //         return;
    //     }

    //     // Image count validation
    //     $maxImages = match ($level) {
    //         'Creator' => 1,
    //         'Influencer' => 4,
    //         default => 0,
    //     };

    //     if ($maxImages === 0 && count($this->images) > 0) {
    //         session()->flash('error', 'You are not allowed to upload images.');
    //         $this->isUploading = false;
    //         return;
    //     }

    //     if (count($this->images) > $maxImages) {
    //         session()->flash('error', "You can upload a maximum of {$maxImages} image(s).");
    //         $this->isUploading = false;
    //         return;
    //     }

    //     // Video validation - can't post both images and videos
    //     if (!empty($this->images) && !empty($this->videos)) {
    //         session()->flash('error', 'You cannot upload both images and videos in the same post.');
    //         $this->isUploading = false;
    //         return;
    //     }

    //     // Check for video permission
    //     if (!empty($this->videos) && !in_array($level, ['Creator', 'Influencer'])) {
    //         session()->flash('error', 'Only Creators and Influencers can upload videos.');
    //         $this->isUploading = false;
    //         return;
    //     }

    //     $user = Auth::user();
    //     $content = $this->convertUrlsToLinks($this->content);
    //     $getContent = Post::where(['user_id' => $user->id])->pluck('content')->toArray();

    //     // Check for similar content
    //     if (isSimilar($content, $getContent, 4)) {
    //         session()->flash('info', 'This content is too similar to existing content, therefore it will not be posted.');
    //         $this->reset('content', 'images', 'videos');
    //         $this->isUploading = false;
    //         return;
    //     }

    //     // Determine post status
    //     $status = 'LIVE';
    //     if ($user->status != 'ACTIVE') {
    //         $status = 'SHADOW_BANNED';
    //     }

    //     // Create post
    //     $uniqueCode = rand(1000, 9999) . time();
    //     $post = Post::create([
    //         'user_id' => $user->id,
    //         'content' => $content,
    //         'unicode' => $uniqueCode,
    //         'comment_external' => 0,
    //         'status' => $status,
    //         'has_video' => !empty($this->videos) ? 1 : 0,
    //         'has_images' => !empty($this->images) ? 1 : 0,
    //     ]);

    //     try {
    //         // Upload images
    //         if (!empty($this->images)) {
    //             $this->uploadImages($post);
    //         }

    //         // Upload videos with adaptive processing
    //         if (!empty($this->videos)) {
    //             dd('Uploading video with adaptive processing...');
    //             $this->uploadVideos($post, $user);
    //         }

    //         session()->flash('success', 'Your post was successful!');
    //         $this->reset('content', 'images', 'videos');
    //     } catch (\Exception $e) {
    //         Log::error('Post upload error: ' . $e->getMessage());
    //         session()->flash('error', 'An error occurred while uploading your media. Please try again.');
    //         $post->delete();
    //     }

    //     $this->isUploading = false;
    // }

    // private function uploadImages($post)
    // {
    //     foreach ($this->images as $image) {
    //         // Determine quality based on network strength
    //         $networkStrength = $this->getUserNetworkStrength();
    //         $quality = $this->getImageQuality($networkStrength);

    //         $uploadedFileUrl = cloudinary()->upload($image->getRealPath(), [
    //             'folder' => 'payhankey_post_images',
    //             'transformation' => [
    //                 ['quality' => $quality, 'fetch_format' => 'auto'],
    //                 ['width' => 1080, 'height' => 1080, 'crop' => 'limit'],
    //             ],
    //             'format' => 'jpg',
    //         ])->getSecurePath();

    //         // Generate thumbnail
    //         $thumbnailUrl = cloudinary()->upload($image->getRealPath(), [
    //             'folder' => 'payhankey_post_images/thumbnails',
    //             'transformation' => [
    //                 ['width' => 300, 'height' => 300, 'crop' => 'fill'],
    //                 ['quality' => 'auto:low'],
    //             ],
    //             'format' => 'jpg',
    //         ])->getSecurePath();

    //         PostImages::create([
    //             'user_id' => Auth::id(),
    //             'post_id' => $post->id,
    //             'path' => $uploadedFileUrl,
    //             'thumbnail_path' => $thumbnailUrl,
    //             'processing_status' => 'completed',
    //         ]);
    //     }
    // }

    // private function uploadVideos($post, $user)
    // {
    //     foreach ($this->videos as $video) {
    //         // Get network strength for adaptive streaming
    //         $networkStrength = $this->getUserNetworkStrength();

    //         // Initial upload with basic processing
    //         $uploadResult = cloudinary()->uploadVideo($video->getRealPath(), [
    //             'folder' => 'payhankey_post_videos',
    //             'resource_type' => 'video',
    //             'eager' => $this->getVideoTransformations($networkStrength),
    //             'eager_async' => true, // Process asynchronously like TikTok/Instagram
    //             'eager_notification_url' => route('cloudinary.webhook'), // Webhook for completion
    //         ]);

    //         $videoUrl = $uploadResult->getSecurePath();
    //         $publicId = $uploadResult->getPublicId();

    //         // Generate thumbnail from video
    //         $thumbnailUrl = cloudinary()->video($publicId)
    //             ->addTransformation(['width' => 640, 'height' => 360, 'crop' => 'fill'])
    //             ->delivery(['format' => 'jpg', 'quality' => 'auto'])
    //             ->toUrl();

    //         // Create video record with processing status
    //         PostVideo::create([
    //             'user_id' => Auth::id(),
    //             'post_id' => $post->id,
    //             'path' => $videoUrl,
    //             'thumbnail_path' => $thumbnailUrl,
    //             'public_id' => $publicId,
    //             'processing_status' => 'processing', // Will be updated via webhook
    //             'duration' => $uploadResult->getResponse()['duration'] ?? null,
    //             'width' => $uploadResult->getResponse()['width'] ?? null,
    //             'height' => $uploadResult->getResponse()['height'] ?? null,
    //             'format' => $uploadResult->getResponse()['format'] ?? null,
    //             'file_size' => $video->getSize(),
    //         ]);
    //     }
    // }

    // private function getUserNetworkStrength()
    // {
    //     // This would ideally come from client-side network detection
    //     // For now, we can use user's connection type or default to medium
    //     // You can implement JavaScript Network Information API and pass to Livewire

    //     $user = Auth::user();

    //     // Check if user has network preference stored
    //     if (isset($user->network_preference)) {
    //         return $user->network_preference;
    //     }

    //     // Default to medium
    //     return 'medium';
    // }

    // private function getImageQuality($networkStrength)
    // {
    //     return match ($networkStrength) {
    //         'slow', '2g' => 'auto:low',
    //         '3g', 'medium' => 'auto:good',
    //         '4g', '5g', 'fast' => 'auto:best',
    //         default => 'auto:good',
    //     };
    // }

    // private function getVideoTransformations($networkStrength)
    // {
    //     // Generate multiple quality versions like TikTok/Instagram
    //     $transformations = [];

    //     switch ($networkStrength) {
    //         case 'slow':
    //         case '2g':
    //             // Low quality only for slow networks
    //             $transformations[] = [
    //                 'width' => 480,
    //                 'height' => 854,
    //                 'crop' => 'limit',
    //                 'quality' => 'auto:low',
    //                 'video_codec' => 'h264',
    //                 'audio_codec' => 'aac',
    //                 'bit_rate' => '500k',
    //             ];
    //             break;

    //         case '3g':
    //         case 'medium':
    //             // Medium and low quality
    //             $transformations[] = [
    //                 'width' => 720,
    //                 'height' => 1280,
    //                 'crop' => 'limit',
    //                 'quality' => 'auto:good',
    //                 'video_codec' => 'h264',
    //                 'audio_codec' => 'aac',
    //                 'bit_rate' => '1500k',
    //             ];
    //             $transformations[] = [
    //                 'width' => 480,
    //                 'height' => 854,
    //                 'crop' => 'limit',
    //                 'quality' => 'auto:low',
    //                 'video_codec' => 'h264',
    //                 'audio_codec' => 'aac',
    //                 'bit_rate' => '500k',
    //             ];
    //             break;

    //         case '4g':
    //         case '5g':
    //         case 'fast':
    //         default:
    //             // High, medium, and low quality (adaptive streaming)
    //             $transformations[] = [
    //                 'width' => 1080,
    //                 'height' => 1920,
    //                 'crop' => 'limit',
    //                 'quality' => 'auto:best',
    //                 'video_codec' => 'h264',
    //                 'audio_codec' => 'aac',
    //                 'bit_rate' => '3000k',
    //             ];
    //             $transformations[] = [
    //                 'width' => 720,
    //                 'height' => 1280,
    //                 'crop' => 'limit',
    //                 'quality' => 'auto:good',
    //                 'video_codec' => 'h264',
    //                 'audio_codec' => 'aac',
    //                 'bit_rate' => '1500k',
    //             ];
    //             $transformations[] = [
    //                 'width' => 480,
    //                 'height' => 854,
    //                 'crop' => 'limit',
    //                 'quality' => 'auto:low',
    //                 'video_codec' => 'h264',
    //                 'audio_codec' => 'aac',
    //                 'bit_rate' => '500k',
    //             ];
    //             break;
    //     }

    //     return $transformations;
    // }

    // private function convertUrlsToLinks($text)
    // {
    //     $pattern = '/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i';
    //     return preg_replace($pattern, '<a href="$0" target="_blank" rel="noopener">$0</a>', $text);
    // }



}
