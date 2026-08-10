<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\CommunityPost;
use App\Services\Admin\AdminCommunityService;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function __construct(private AdminCommunityService $communities) {}

    public function index(Request $request)
    {
        $search = $request->string('q')->trim()->toString() ?: null;
        $type = $request->string('type')->trim()->toString() ?: null;
        $currency = $request->string('currency')->trim()->toString() ?: null;

        return view('admin.communities.index', [
            'communities' => $this->communities->list($search, $type, $currency),
            'stats' => $this->communities->dashboardStats(),
            'search' => $search ?? '',
            'type' => $type ?? '',
            'currency' => $currency ?? '',
        ]);
    }

    public function show(Request $request, Community $community)
    {
        $community = $this->communities->show($community->id);
        $tab = $request->string('tab')->trim()->toString() ?: 'overview';

        $data = [
            'community' => $community,
            'tab' => $tab,
            'revenueSummary' => $this->communities->revenueSummary($community),
            'paymentPlans' => $this->communities->paymentPlans($community),
        ];

        return match ($tab) {
            'members' => view('admin.communities.show', array_merge($data, [
                'members' => $this->communities->members($community),
            ])),
            'subscriptions' => view('admin.communities.show', array_merge($data, [
                'subscriptions' => $this->communities->subscriptions($community),
            ])),
            'payouts' => view('admin.communities.show', array_merge($data, [
                'payouts' => $this->communities->payouts($community),
            ])),
            'posts' => view('admin.communities.show', array_merge($data, [
                'posts' => $this->communities->posts($community),
            ])),
            'invites' => view('admin.communities.show', array_merge($data, [
                'invites' => $this->communities->invites($community),
                'joinRequests' => $this->communities->joinRequests($community),
            ])),
            default => view('admin.communities.show', $data),
        };
    }

    public function archive(Community $community)
    {
        $this->communities->archive($community);

        return back()->with('success', 'Community archived.');
    }

    public function unarchive(Community $community)
    {
        $this->communities->unarchive($community);

        return back()->with('success', 'Community restored.');
    }

    public function destroy(Community $community)
    {
        $this->communities->delete($community);

        return redirect()
            ->route('admin.communities.index')
            ->with('success', 'Community deleted.');
    }

    public function banMember(Request $request, Community $community)
    {
        $request->validate(['user_id' => 'required|uuid|exists:users,id']);

        if (! $this->communities->banMember($community, $request->user_id)) {
            return back()->with('error', 'Unable to ban that member.');
        }

        return back()->with('success', 'Member banned.');
    }

    public function unbanMember(Request $request, Community $community)
    {
        $request->validate(['user_id' => 'required|uuid|exists:users,id']);

        if (! $this->communities->unbanMember($community, $request->user_id)) {
            return back()->with('error', 'Unable to unban that member.');
        }

        return back()->with('success', 'Member unbanned.');
    }

    public function removeMember(Request $request, Community $community)
    {
        $request->validate(['user_id' => 'required|uuid|exists:users,id']);

        if (! $this->communities->removeMember($community, $request->user_id)) {
            return back()->with('error', 'Unable to remove that member.');
        }

        return back()->with('success', 'Member removed.');
    }

    public function destroyPost(Community $community, CommunityPost $post)
    {
        if ($post->community_id !== $community->id) {
            abort(404);
        }

        if (! $this->communities->deletePost($community, $post->id)) {
            return back()->with('error', 'Unable to delete that post.');
        }

        return back()->with('success', 'Post deleted.');
    }
}
