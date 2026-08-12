<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\GeneralMail;
use App\Models\AccessCode;
use App\Models\Comment;
use App\Models\CommentExternal;
use App\Models\CommentExternalMessage;
use App\Models\Community;
use App\Models\EngagementDailyStat;
use App\Models\EngagementMonthlyStat;
use App\Models\FremiumEngagementStat;
use App\Models\Level;
use App\Models\Payout;
use App\Models\Post;
use App\Models\SubscriptionStat;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserComment;
use App\Models\UserLevel;
use App\Models\UserLike;
use App\Models\UserView;
use App\Models\ViewsExternal;
use App\Services\PublicSiteService;
use App\Models\WithdrawalMethod;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Stevebauman\Location\Facades\Location;

class GeneralController extends Controller
{
    public function __construct(
        protected PublicSiteService $publicSite,
    ) {}

    public function landingpage()
    {
        return view('general.landingpage', [
            'previewEarners' => $this->publicSite->landingPreviewEarners(3),
            'platformStats' => $this->publicSite->platformStats(),
        ]);
    }

    public function features()
    {
        return view('general.features');
    }

    public function how()
    {
        return view('general.how');
    }
    public function about()
    {
        return view('general.about');
    }

    public function contact()
    {
        return view('general.contact');
    }

    public function submitContact(ContactRequest $request)
    {
        $data = $request->validated();

        Mail::to(config('payhankey.support_email'))->send(new GeneralMail(
            (object) ['name' => $data['name'], 'email' => $data['email']],
            'Contact form: ' . $data['subject'],
            "From: {$data['name']} ({$data['email']})\n\nSubject: {$data['subject']}\n\n{$data['message']}"
        ));

        return back()->with('success', 'Thanks! Your message was sent — we\'ll reply within 24 hours.');
    }
    public function blog()
    {
        return view('general.blog');
    }

    public function privacyPolicy()
    {
        return view('general.privacy');
    }

    public function terms()
    {
        return view('general.terms');
    }

    public function howToEarn()
    {
        return view('general.earn');
    }

    public function test()
    {
        return ipLocation();
    }

    //  public function __invoke(Community $community)
    // {
    //     $community->loadCount('members')->load(['category', 'user']);

    //     return view('public.community-show', [
    //         'community' => $community,
    //     ]);
    // }

    public function communityPublic(Community $community)
    {
        if ($community->isArchived()) {
            abort(404);
        }

        $community->loadCount(['members', 'posts'])->load(['category', 'user']);

        return view('community.publics', ['community' => $community]);
    }

    public function topEarners()
    {
        $earners = $this->publicSite->topEarners(10);

        return view('general.top-earner', [
            'lastMonthEarners' => $earners['lastMonth'],
            'allTimeEarners' => $earners['allTime'],
        ]);
    }


    public function ipConfig()
    {
        

    //     $month = now()->subMonth()->format('Y-m');

    //     $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
    //     $endDate   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

    //     $stats = EngagementDailyStat::whereBetween(
    //         'date',
    //         [
    //             $startDate,
    //             $endDate,
    //         ]
    //     )
    //         ->groupBy('user_id', 'level')
    //         ->selectRaw('
    //     user_id,
    //     level,
    //     SUM(views) as views,
    //     SUM(likes) as likes,
    //     SUM(comments) as comments,
    //     SUM(points) as points
    // ')
    //         ->get();

    //     $processedUsers = 0;
    //     $createdRecords = 0;
    //     $updatedRecords = 0;
    //     $results = [];

    //     foreach ($stats as $stat) {

    //         $processedUsers++;
            

    //         $monthlyStat = EngagementMonthlyStat::updateOrCreate(

            
    //             [
    //                 'user_id' => $stat->user_id,
    //                 'level'   => $stat->level,
    //                 'month'   => $month,
    //             ],
    //             [
    //                 'views'    => $stat->views,
    //                 'likes'    => $stat->likes,
    //                 'comments' => $stat->comments,
    //                 'points'   => $stat->points,
    //             ]
    //         );

    //         if ($monthlyStat->wasRecentlyCreated) {
    //             $createdRecords++;
    //         } else {
    //             $updatedRecords++;
    //         }

    //         $results[] = [
    //             'user_id' => $stat->user_id,
    //             'level' => $stat->level,
    //             'month' => $month,
    //             'views' => $stat->views,
    //             'likes' => $stat->likes,
    //             'comments' => $stat->comments,
    //             'points' => $stat->points,
    //         ];
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Monthly engagement stats generated successfully',
    //         'summary' => [
    //             'month' => $month,
    //             'date_range' => [
    //                 'from' => $startDate->toDateString(),
    //                 'to' => $endDate->toDateString(),
    //             ],
    //             'users_processed' => $processedUsers,
    //             'monthly_stats_created' => $createdRecords,
    //             'monthly_stats_updated' => $updatedRecords,
    //             'total_records' => count($results),
    //         ],
    //         'data' => $results,
    //     ], 200, [], JSON_PRETTY_PRINT);
    }

    // public function ipConfig()
    // {

    //     $startDate = Carbon::create(2026, 6, 15);
    //     $endDate   = Carbon::create(2026, 6, 30);

    //     $period = CarbonPeriod::create($startDate, $endDate);

    //     // $this->info("========================================");
    //     // $this->info("Starting Engagement Stats Backfill");
    //     // $this->info("Date Range: {$startDate->toDateString()} -> {$endDate->toDateString()}");
    //     // $this->info("========================================");

    //     $activeUsers = UserLevel::where('status', 'active')
    //         ->whereIn('plan_name', ['Creator', 'Influencer'])
    //         ->get();

    //     // $this->info("Found {$activeUsers->count()} active users.");

    //     $results = [];

    //     foreach ($period as $day) {

    //         $date = $day->toDateString();

    //         // $this->newLine();
    //         // $this->info("Processing Date: {$date}");
    //         // $this->info(str_repeat('-', 60));

    //         foreach ($activeUsers as $userLevel) {

    //             // $this->line("Checking User #{$userLevel->user_id} ({$userLevel->plan_name})...");

    //             DB::transaction(function () use ($userLevel, $date, &$results) {

    //                 // Skip if record already exists
    //                 if (
    //                     EngagementDailyStat::where('user_id', $userLevel->user_id)
    //                     ->whereDate('date', $date)
    //                     ->exists()
    //                 ) {
    //                     $this->warn("   ↳ Already exists. Skipping.");
    //                     return;
    //                 }

    //                 // Count views
    //                 $views = UserView::where('poster_user_id', $userLevel->user_id)
    //                     ->whereDate('created_at', $date)
    //                     ->where('type', 'view')
    //                     ->count();

    //                 // Count likes
    //                 $likes = UserLike::where('poster_user_id', $userLevel->user_id)
    //                     ->whereDate('created_at', $date)
    //                     ->where('type', 'like')
    //                     ->count();

    //                 // Count comments
    //                 $comments = UserComment::where('poster_user_id', $userLevel->user_id)
    //                     ->whereDate('created_at', $date)
    //                     ->where('type', 'comment')
    //                     ->count();

    //                 $points = $views + $likes + $comments;

    //                 $data = [
    //                     'date'      => $date,
    //                     'user_id'   => $userLevel->user_id,
    //                     'level'     => $userLevel->plan_name,
    //                     'views'     => $views,
    //                     'likes'     => $likes,
    //                     'comments'  => $comments,
    //                     'points'    => $points,
    //                 ];

    //                 // Store for final JSON output
    //                 $results[] = $data;

    //                 // Display JSON
    //                 // $this->line(json_encode($data, JSON_PRETTY_PRINT));

    //                 if ($points === 0) {
    //                     // $this->warn("   ↳ No engagement found. Skipping insert.");
    //                     return;
    //                 }

    //                 EngagementDailyStat::create($data);

    //                 // $this->info("   ✓ Saved successfully.");
    //             });
    //         }
    //     }

    //     // $this->newLine();
    //     // $this->info("========================================");
    //     // $this->info("Backfill Completed");
    //     // $this->info("========================================");

    //     // $this->newLine();
    //     // $this->info("Summary JSON:");

    //     // $this->line(json_encode($results, JSON_PRETTY_PRINT));



    // }

    public function validateCode()
    {
        $level = Level::all();
        return view('send_access_code', ['levels' => $level]);
    }

    public function processValidateCode(Request $request)
    {
        if ($request->validationCode == 'LONZETY') {
            $code = $this->generateCode(7);

            $ref = time();
            $level = Level::where('id', $request->level)->first();

            $chekIfNotRedeemed = AccessCode::where('email', $request->email)->where('is_active', true)->first();

            if ($chekIfNotRedeemed) {
                return redirect('error');
            }

            AccessCode::create(['tx_id' => $ref, 'name' => $level->name, 'email' => $request->email, 'amount' => $level->amount, 'code' => $code, 'level_id' => $level->id]);
            Mail::to($request->email)->send(new AccessCodeMail($code));

            return redirect('success');
        } else {
            return 'no show';
        }
    }


    public function generateCode($number)
    {
        $alph = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $code = '';

        for ($i = 0; $i < $number; $i++) {
            $code .= $alph[rand(0, 35)];
        }

        return $code;
    }

    public function success()
    {

        return view('successful');
    }

    public function error()
    {
        return 'Error processing payment';
    }

    public function showPost($id)
    {

        // $timeline = Post::with(['postComments' => function($query) {
        //     $query->paginate(15); // Load initial 15 comments
        // }])->where('id', $id)->firstOrFail();

        $timeline = Post::with(['postCommentsExternal' => function ($query) {
            $query->paginate(15); // Load initial 15 comments
        }])->where('id', $id)->firstOrFail();



        $timeline->views_external += 1;
        $timeline->save();

        //unique view
        $location = ipLocation();
        $checkunique = ViewsExternal::where(['post_id' => $id, 'ip' => $location['ip'], 'city' => $location['city']])->first();
        if (!$checkunique) {
            ViewsExternal::create(['post_id' => $id, 'ip' => $location['ip'], 'city' => $location['city']]);
        }


        return view('showpost', ['timeline' => $timeline]);
    }

    public function comment(Request $request)
    {

        //unique view
        $location = ipLocation();

        $timeline = Post::where('id', $request->post_id)->first();

        //    $timeline = Post::with(['postCommentsExternal' => function($query) {
        //     $query->paginate(15); // Load initial 15 comments
        // }])->where('id', $request->post_id)->firstOrFail();


        $timeline->comment_external += 1;
        $timeline->save();

        CommentExternalMessage::create(['post_id' => $request->post_id, 'message' => $request->message]);

        $checkunique = CommentExternal::where(['post_id' => $request->post_id, 'ip' => $location['ip'], 'city' => $location['city']])->first();
        if (!$checkunique) {
            CommentExternal::create(['post_id' => $request->post_id, 'message' => $request->message, 'ip' => $location['ip'], 'city' => $location['city']]);

            // CommentExternal::create(['post_id' => $request->post_id, 'ip' => $location['ip'], 'city' => $location['city']]);
        }

        return  back();
    }

    public function loadMoreComments(Request $request)
    {
        $postId = $request->post_id;
        $page = $request->page;

        $comments = Post::findOrFail($postId)->postComments()->paginate(5, ['*'], 'page', $page);

        return response()->json([
            'comments' => $comments->items(),
            'next_page_url' => $comments->nextPageUrl()
        ]);
    }

    public function userList()
    {
        $users = User::role('user')->orderBy('created_at', 'desc')->get();
        return view('user_list', ['users' => $users]);
    }

    public function access()
    {
        $acc = AccessCode::where('is_active', true)->get();
        return view('access', ['access' => $acc]);
    }
}
