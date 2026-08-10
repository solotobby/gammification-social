<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoUploadService
{
    public function maxSeconds(string $level): int
    {
        return match ($level) {
            'Influencer' => 180,
            'Creator'    => 60,
            default      => 0,
        };
    }

    public function maxFileKb(string $level): int
    {
        return match ($level) {
            'Influencer' => 512000,
            'Creator'    => 256000,
            default      => 0,
        };
    }

    public function allowedMimetypes(): string
    {
        return 'video/mp4,video/quicktime,video/x-msvideo,video/webm,video/x-matroska,video/3gpp,video/mpeg,video/x-flv';
    }

    /**
     * Process and upload a video to DigitalOcean Spaces.
     *
     * @return array{
     *   url: string,
     *   public_id: string,
     *   thumbnail: ?string,
     *   duration: ?int,
     *   width: ?int,
     *   height: ?int,
     *   format: ?string,
     *   file_size: ?int,
     *   quality_versions: array<string, string>
     * }
     */
    public function upload(string $realPath, string $level, ?string $userId = null): array
    {
        $maxSeconds = $this->maxSeconds($level);

        if ($maxSeconds === 0) {
            throw new \RuntimeException('Your account level cannot upload videos.');
        }

        set_time_limit(0);

        $userId = $userId ?? (string) Auth::id();
        $token  = (string) Str::uuid();
        $prefix = "payhankey_videos/{$userId}/{$token}";
        $work   = storage_path('app/video-work/'.$token);

        File::ensureDirectoryExists($work);

        try {
            $extension = $this->guessExtension($realPath);
            $source    = $work.'/source.'.$extension;
            File::copy($realPath, $source);

            $meta = $this->probe($source);

            if (($meta['duration'] ?? 0) > $maxSeconds) {
                $trimmed = $work.'/trimmed.mp4';
                if ($this->hasFfmpeg() && $this->runFfmpeg([
                    '-y', '-i', $source,
                    '-t', (string) $maxSeconds,
                    '-c', 'copy',
                    $trimmed,
                ])) {
                    $source = $trimmed;
                    $meta['duration'] = $maxSeconds;
                }
            }

            $qualityVersions = [];
            $disk            = Storage::disk('spaces');

            if ($this->hasFfmpeg()) {
                $variants = [
                    'high'   => ['file' => 'high.mp4',   'width' => 1080, 'crf' => '22'],
                    'medium' => ['file' => 'medium.mp4', 'width' => 720,  'crf' => '24'],
                    'low'    => ['file' => 'low.mp4',    'width' => 480,  'crf' => '28'],
                ];

                foreach ($variants as $label => $cfg) {
                    $local = $work.'/'.$cfg['file'];
                    if ($this->transcode($source, $local, (int) $cfg['width'], $cfg['crf'], $maxSeconds)) {
                        $key = $prefix.'/'.$cfg['file'];
                        $disk->put($key, fopen($local, 'r'), ['visibility' => 'public']);
                        $qualityVersions[$label] = $this->spacesUrl($key);
                    }
                }

                $posterLocal = $work.'/poster.jpg';
                if ($this->extractPoster($source, $posterLocal)) {
                    $posterKey = $prefix.'/poster.jpg';
                    $disk->put($posterKey, fopen($posterLocal, 'r'), ['visibility' => 'public']);
                    $thumbnail = $this->spacesUrl($posterKey);
                }
            }

            if ($qualityVersions === []) {
                $originalKey = $prefix.'/original.'.$extension;
                $disk->put($originalKey, fopen($source, 'r'), ['visibility' => 'public']);
                $url = $this->spacesUrl($originalKey);
                $qualityVersions = [
                    'high'   => $url,
                    'medium' => $url,
                    'low'    => $url,
                ];
            }

            $primaryUrl = $qualityVersions['medium']
                ?? $qualityVersions['high']
                ?? reset($qualityVersions);

            return [
                'url'              => $primaryUrl,
                'public_id'        => $prefix,
                'thumbnail'        => $thumbnail ?? null,
                'duration'         => isset($meta['duration']) ? (int) round((float) $meta['duration']) : null,
                'width'            => $meta['width'] ?? null,
                'height'           => $meta['height'] ?? null,
                'format'           => $meta['format'] ?? $extension,
                'file_size'        => $meta['size'] ?? (file_exists($source) ? filesize($source) : null),
                'quality_versions' => $qualityVersions,
            ];
        } finally {
            File::deleteDirectory($work);
        }
    }

    public function delete(string $storagePrefix): void
    {
        if ($storagePrefix === '') {
            return;
        }

        $disk = Storage::disk('spaces');

        foreach ($disk->allFiles($storagePrefix) as $file) {
            $disk->delete($file);
        }
    }

    private function spacesUrl(string $key): string
    {
        $base = rtrim((string) config('filesystems.disks.spaces.url'), '/');

        return $base.'/'.ltrim($key, '/');
    }

    private function guessExtension(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($ext, ['mp4', 'mov', 'webm', 'avi', 'mkv', 'mpeg', 'mpg', '3gp'], true)
            ? $ext
            : 'mp4';
    }

    private function hasFfmpeg(): bool
    {
        static $cached = null;

        if ($cached !== null) {
            return $cached;
        }

        $ffmpeg = trim((string) shell_exec('command -v ffmpeg 2>/dev/null'));

        return $cached = $ffmpeg !== '';
    }

    private function probe(string $path): array
    {
        $meta = [
            'duration' => null,
            'width'    => null,
            'height'   => null,
            'format'   => pathinfo($path, PATHINFO_EXTENSION),
            'size'     => file_exists($path) ? filesize($path) : null,
        ];

        if (! $this->hasFfmpeg()) {
            return $meta;
        }

        $cmd = sprintf(
            'ffprobe -v quiet -print_format json -show_format -show_streams %s 2>/dev/null',
            escapeshellarg($path)
        );

        $json = json_decode((string) shell_exec($cmd), true);
        if (! is_array($json)) {
            return $meta;
        }

        if (isset($json['format']['duration'])) {
            $meta['duration'] = (float) $json['format']['duration'];
        }

        foreach ($json['streams'] ?? [] as $stream) {
            if (($stream['codec_type'] ?? '') === 'video') {
                $meta['width']  = isset($stream['width']) ? (int) $stream['width'] : null;
                $meta['height'] = isset($stream['height']) ? (int) $stream['height'] : null;
                break;
            }
        }

        return $meta;
    }

    private function transcode(string $input, string $output, int $maxWidth, string $crf, int $maxSeconds): bool
    {
        $scale = "scale='min({$maxWidth},iw)':-2";

        return $this->runFfmpeg([
            '-y', '-i', $input,
            '-t', (string) $maxSeconds,
            '-vf', $scale,
            '-c:v', 'libx264',
            '-preset', 'fast',
            '-crf', $crf,
            '-c:a', 'aac',
            '-b:a', '128k',
            '-movflags', '+faststart',
            '-pix_fmt', 'yuv420p',
            $output,
        ]);
    }

    private function extractPoster(string $input, string $output): bool
    {
        return $this->runFfmpeg([
            '-y', '-i', $input,
            '-ss', '00:00:00.5',
            '-vframes', '1',
            '-q:v', '2',
            $output,
        ]);
    }

    private function runFfmpeg(array $args): bool
    {
        $cmd = 'ffmpeg '.implode(' ', array_map('escapeshellarg', $args)).' 2>&1';
        exec($cmd, $out, $code);

        return $code === 0;
    }
}
