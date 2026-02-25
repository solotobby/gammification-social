<?php

namespace App\Services;

use App\Models\Post;
use App\Models\TrendingTopic;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TrendingTopicService
{
    public function genenrateTrendingTopics()
    {

        $posts = Post::where('created_at', '>=', now()->subHours(6))
            ->select('user_id', 'content', 'likes', 'comments', 'comment_external', 'views', 'views_external', 'has_images', 'created_at')
            ->get();

        $trendData = [];

        foreach ($posts as $post) {

            $phrases = $this->extractPhrases($post->content);

            // Engagement velocity (recent posts get boost)
            $minutesOld = now()->diffInMinutes($post->created_at);
            $velocityBoost = max(1, (360 - $minutesOld) / 60);

            $engagementScore =
                ($post->likes * 0.4) +
                ($post->comments * 0.6) +
                ($post->comment_external * 0.2) +
                ($post->views * 0.2) +
                ($post->views_external * 0.05);

            $imageBoost = $post->has_images === 1 ? 5 : 0;

            foreach (array_unique($phrases) as $phrase) {

                if (strlen($phrase) < 4) continue;

                if (!isset($trendData[$phrase])) {
                    $trendData[$phrase] = [
                        'score' => 0,
                        'users' => []
                    ];
                }

                $trendData[$phrase]['score'] +=
                    1 +
                    ($engagementScore * 0.8) +
                    $velocityBoost +
                    $imageBoost +
                    (str_starts_with($phrase, '#') ? 3 : 0);

                $trendData[$phrase]['users'][$post->user_id] = true;
            }
        }

        // Add uniqueness boost
        foreach ($trendData as $phrase => &$data) {
            $uniqueUsers = count($data['users']);
            $data['score'] += $uniqueUsers * 2;
        }

        // Sort
        uasort($trendData, fn($a, $b) => $b['score'] <=> $a['score']);

        $topFive = collect($trendData)->take(5); //take top 5

        DB::transaction(function () use ($topFive) {

            // Clear previous trends (atomic refresh)
            TrendingTopic::truncate();

            foreach ($topFive as $phrase => $data) {

                TrendingTopic::create([
                    'phrase' => $phrase,
                    'slug' => Str::slug($phrase),
                    'score' => round($data['score'], 2),
                    'timeframe' => '6_hours'
                ]);
            }
        });

        // return collect($trendData)
        //     ->take(10)
        //     ->map(fn($data, $phrase) => [
        //         'topic' => '#' . ucfirst($phrase),
        //         'score' => round($data['score'], 2)
        //     ])
        //     ->values();
    }

    private function extractPhrases(string $text): array
    {
        // 1️⃣ Lowercase everything
        $text = strtolower($text);

        // 2️⃣ Remove URLs / anything that looks like a URL
        $text = preg_replace('/https?:\/\/\S+|www\.\S+|\S+\.(com|net|org|io|gov|edu|ng|co\.\w+)/', '', $text);

        // 3️⃣ Remove non-word characters except hashtags
        $text = preg_replace('/[^\w\s#]/', '', $text);

        // 4️⃣ Split into words
        $words = array_values(array_filter(explode(' ', $text)));

        $phrases = [];

        // 5️⃣ Extract hashtags
        preg_match_all('/#\w+/', $text, $hashtags);
        $phrases = array_merge($phrases, $hashtags[0]);

        // 6️⃣ Bigrams
        for ($i = 0; $i < count($words) - 1; $i++) {
            $phrases[] = $words[$i] . ' ' . $words[$i + 1];
        }

        // 7️⃣ Trigrams
        for ($i = 0; $i < count($words) - 2; $i++) {
            $phrases[] = $words[$i] . ' ' . $words[$i + 1] . ' ' . $words[$i + 2];
        }

        return $phrases;
    }
}
