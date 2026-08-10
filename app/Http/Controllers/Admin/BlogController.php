<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Services\AdminAuditService;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function __construct(private AdminAuditService $audit) {}

    public function create()
    {
        $category = BlogCategory::all();

        return view('admin.blog.create', ['category' => $category]);
    }

    public function list()
    {
        $baseQuery = Blog::query()->with('blogCategory:id,name');

        $totalPosts = (clone $baseQuery)->count();
        $totalPublished = (clone $baseQuery)->where('status', 'PUBLISHED')->count();
        $totalDrafts = (clone $baseQuery)->where('status', 'DRAFT')->count();

        $paginatedBlogs = (clone $baseQuery)
            ->latest()
            ->select('id', 'title', 'status', 'slug', 'published_at', 'created_at')
            ->paginate(10);

        return view('admin.blog.index', [
            'totalPosts' => $totalPosts,
            'totalPublished' => $totalPublished,
            'totalDrafts' => $totalDrafts,
            'blogs' => $paginatedBlogs,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|min:300',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $coverPath = null;

        if ($request->hasFile('cover_image')) {
            $uploadedFile = Cloudinary::upload(
                $request->file('cover_image')->getRealPath(),
                [
                    'folder' => 'payhankey/blogs',
                    'transformation' => [
                        'width' => 1200,
                        'height' => 630,
                        'crop' => 'fill',
                        'quality' => 'auto',
                        'fetch_format' => 'auto',
                    ],
                ]
            );

            $coverPath = $uploadedFile->getSecurePath();
        }

        $blog = Blog::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'blog_category_id' => $request->blog_category_id,
            'excerpt' => Str::limit(strip_tags($request->content), 160),
            'content' => $request->content,
            'cover_image' => $coverPath,
            'status' => 'PUBLISHED',
            'published_at' => now(),
        ]);

        $this->audit->log('blog.created', $blog);

        return back()->with('success', 'Blog posted and published successfully.');
    }

    public function deletePost($slug)
    {
        $blog = Blog::query()->where('slug', $slug)->firstOrFail();
        $blog->delete();

        $this->audit->log('blog.deleted', $blog, [
            'slug' => $slug,
        ]);

        return back()->with('success', 'Blog post deleted successfully.');
    }
}
