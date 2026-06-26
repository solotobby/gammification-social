<?php

namespace App\Services;

use App\Models\Hashtag;

class TrendingHashTags
{



    public function getTrending()
    {

        return Hashtag::select(
            'hashtags.*'
        )

            ->join(
                'hashtag_trends',
                'hashtags.id',
                '=',
                'hashtag_trends.hashtag_id'
            )


            ->where(
                'hashtag_trends.created_at',
                '>',
                now()->subHours(24)
            )


            ->selectRaw("
                    SUM(
                    hashtag_trends.score *
                    EXP(
                    -(TIMESTAMPDIFF(
                    MINUTE,
                    hashtag_trends.created_at,
                    NOW()
                    )/60)
                    )
                    )
                    AS trend_score
                    ")


            ->groupBy('hashtags.id')


            ->orderByDesc(
                'trend_score'
            )


            ->limit(20)

            ->get();
    }
}
