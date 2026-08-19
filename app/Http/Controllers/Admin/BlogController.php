<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Services\AdminAuditService;
use App\Services\ImageUploadService;
use App\Support\StoredMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function __construct(
        private AdminAuditService $audit,
        private ImageUploadService $images,
    ) {}

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
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:'.$this->images->maxFileKb(),
        ]);

        $coverPath = null;

        if ($request->hasFile('cover_image')) {
            $coverPath = $this->uploadCoverImage($request->file('cover_image'));
        }

        $blog = Blog::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'blog_category_id' => $request->blog_category_id,
            'excerpt' => $request->filled('excerpt')
                ? Str::limit(strip_tags($request->excerpt), 160)
                : Str::limit(strip_tags($request->content), 160),
            'content' => $request->content,
            'cover_image' => $coverPath,
            'status' => 'PUBLISHED',
            'published_at' => now(),
        ]);

        $this->audit->log('blog.created', $blog);

        return back()->with('success', 'Blog posted and published successfully.');
    }

    public function edit(string $slug)
    {
        $blog = Blog::query()->where('slug', $slug)->firstOrFail();
        $category = BlogCategory::all();

        return view('admin.blog.edit', [
            'blog' => $blog,
            'category' => $category,
        ]);
    }

    public function update(Request $request, string $slug)
    {
        $blog = Blog::query()->where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|min:300',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:'.$this->images->maxFileKb(),
            'remove_cover_image' => 'nullable|boolean',
        ]);

        $coverPath = $blog->cover_image;

        if ($request->boolean('remove_cover_image') && ! $request->hasFile('cover_image')) {
            StoredMedia::delete($blog->cover_image);
            $coverPath = null;
        }

        if ($request->hasFile('cover_image')) {
            StoredMedia::delete($blog->cover_image);
            $coverPath = $this->uploadCoverImage($request->file('cover_image'));
        }

        $blog->update([
            'title' => $validated['title'],
            'blog_category_id' => $validated['blog_category_id'],
            'excerpt' => filled($validated['excerpt'] ?? null)
                ? Str::limit(strip_tags($validated['excerpt']), 160)
                : Str::limit(strip_tags($validated['content']), 160),
            'content' => $validated['content'],
            'cover_image' => $coverPath,
        ]);

        $this->audit->log('blog.updated', $blog);

        return redirect()
            ->route('admin.blog.edit', $blog->slug)
            ->with('success', 'Blog post updated successfully.');
    }

    public function deletePost($slug)
    {
        $blog = Blog::query()->where('slug', $slug)->firstOrFail();
        StoredMedia::delete($blog->cover_image);
        $blog->delete();

        $this->audit->log('blog.deleted', $blog, [
            'slug' => $slug,
        ]);

        return back()->with('success', 'Blog post deleted successfully.');
    }

    private function uploadCoverImage($file): string
    {
        return $this->images->upload($file, 'payhankey_media/blogs');
    }
}
