<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PostVideo;

use Illuminate\Support\Facades\Log;

class CloudinaryWebhookController extends Controller
{
     public function handleVideoProcessing(Request $request)
    {
        // Verify webhook signature (important for security)
        if (!$this->verifySignature($request)) {
            Log::warning('Invalid Cloudinary webhook signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $notification = $request->all();
        
        Log::info('Cloudinary webhook received', $notification);

        try {
            $publicId = $notification['public_id'] ?? null;
            
            if (!$publicId) {
                return response()->json(['error' => 'Missing public_id'], 400);
            }

            // Find the video by public_id
            $video = PostVideo::where('public_id', $publicId)->first();

            if (!$video) {
                Log::warning("Video not found for public_id: {$publicId}");
                return response()->json(['error' => 'Video not found'], 404);
            }

            // Check if processing was successful
            if ($notification['notification_type'] === 'eager' && isset($notification['eager'])) {
                $qualityVersions = [];
                
                foreach ($notification['eager'] as $transformation) {
                    $url = $transformation['secure_url'] ?? $transformation['url'] ?? null;
                    
                    if ($url) {
                        // Determine quality based on transformation
                        $quality = $this->determineQuality($transformation);
                        $qualityVersions[$quality] = $url;
                    }
                }

                // Update video with quality versions
                $video->update([
                    'quality_versions' => $qualityVersions,
                    'processing_status' => 'completed',
                ]);

                Log::info("Video processing completed for: {$publicId}");
            } elseif (isset($notification['error'])) {
                // Processing failed
                $video->update([
                    'processing_status' => 'failed',
                ]);

                Log::error("Video processing failed for: {$publicId}", [
                    'error' => $notification['error']
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error processing Cloudinary webhook: ' . $e->getMessage());
            return response()->json(['error' => 'Processing error'], 500);
        }
    }

    /**
     * Verify Cloudinary webhook signature
     */
    private function verifySignature(Request $request)
    {
        // Implement signature verification based on Cloudinary's documentation
        // This is a placeholder - implement according to your Cloudinary setup
        
        $timestamp = $request->header('X-Cld-Timestamp');
        $signature = $request->header('X-Cld-Signature');
        
        if (!$timestamp || !$signature) {
            return false;
        }

        // Verify the signature using your API secret
        $apiSecret = config('cloudinary.api_secret');
        $payload = $request->getContent();
        
        $expectedSignature = hash_hmac('sha256', $timestamp . $payload, $apiSecret);
        
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Determine quality level from transformation parameters
     */
    private function determineQuality($transformation)
    {
        $width = $transformation['width'] ?? 0;
        
        if ($width >= 1080) {
            return 'high';
        } elseif ($width >= 720) {
            return 'medium';
        } else {
            return 'low';
        }
    }
}
