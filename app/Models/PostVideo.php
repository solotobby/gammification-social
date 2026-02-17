<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostVideo extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

    protected $fillable = [
        'user_id',
        'post_id',
        'path',
        'thumbnail_path',
        'public_id',
        'duration',
        'width',
        'height',
        'format',
        'file_size',
        'processing_status',
        'quality_versions',
        'view_count',
        'play_count',
        'avg_watch_time',
    ];

    protected $casts = [
        'quality_versions' => 'array',
        'duration' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'file_size' => 'integer',
        'view_count' => 'integer',
        'play_count' => 'integer',
        'avg_watch_time' => 'decimal:2',
    ];

    /**
     * Get the post that owns the video.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the user that uploaded the video.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get video URL for specific quality
     */
    public function getQualityUrl($quality = 'high')
    {
        if ($this->quality_versions && isset($this->quality_versions[$quality])) {
            return $this->quality_versions[$quality];
        }
        
        return $this->path; // Fallback to original
    }

    /**
     * Get adaptive video URL based on network strength
     */
    public function getAdaptiveUrl($networkStrength = 'medium')
    {
        $qualityMap = [
            'slow' => 'low',
            '2g' => 'low',
            '3g' => 'medium',
            'medium' => 'medium',
            '4g' => 'high',
            '5g' => 'high',
            'fast' => 'high',
        ];

        $quality = $qualityMap[$networkStrength] ?? 'medium';
        return $this->getQualityUrl($quality);
    }

    /**
     * Increment view count
     */
    public function incrementViews()
    {
        $this->increment('view_count');
    }

    /**
     * Increment play count
     */
    public function incrementPlays()
    {
        $this->increment('play_count');
    }

    /**
     * Update average watch time
     */
    public function updateWatchTime($watchTime)
    {
        $totalWatchTime = ($this->avg_watch_time * $this->play_count) + $watchTime;
        $newPlayCount = $this->play_count + 1;
        
        $this->update([
            'avg_watch_time' => $totalWatchTime / $newPlayCount,
            'play_count' => $newPlayCount,
        ]);
    }

    /**
     * Check if video processing is complete
     */
    public function isProcessed()
    {
        return $this->processing_status === 'completed';
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) {
            return '0:00';
        }

        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return '0 MB';
        }

        $size = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2) . ' ' . $units[$unitIndex];
    }
}