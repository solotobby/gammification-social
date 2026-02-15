<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class BlogController extends Controller
{
    public function create()
    {
        $category = BlogCategory::all();
        return view('admin.blog.create', ['category' => $category]);
    }

    public function list()
    {
        // $blogs = Blog::where('status','PUBLISHED')
        //     ->latest()
        //     ->paginate(10);

        $blogs = Blog::with('blogCategory:id,name');
        $totalPosts     = $blogs->count();
        $totalPublished = (clone $blogs)->where('status', 'PUBLISHED')->count();
        $totalDrafts    = (clone $blogs)->where('status', 'DRAFT')->count();

        $paginatedBlogs = (clone $blogs)->latest()
            ->select('id', 'title', 'status', 'slug', 'published_at')
            ->paginate(10);

        return view('admin.blog.index', [
            'totalPosts'     => $totalPosts,
            'totalPublished' => $totalPublished,
            'totalDrafts'    => $totalDrafts,
            'blogs'          => $paginatedBlogs,
        ]);
    }

    public function store(Request $request)
    {
        // return $request;

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
                        'width'  => 1200,
                        'height' => 630,
                        'crop'   => 'fill',
                        'quality' => 'auto',
                        'fetch_format' => 'auto'
                    ]
                ]
            );

            $coverPath = $uploadedFile->getSecurePath();
        }


        Blog::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'blog_category_id' => $request->blog_category_id,
            'excerpt' => Str::limit(strip_tags($request->content), 160),
            'content' => $request->content,
            'cover_image' => $coverPath,
            'status' => 'PUBLISHED',
            'published_at' => now(),
        ]);

        return back()->with('success', 'Blog Posted and published Successfully');
    }

    public function deletePost($slug){
        Blog::where('slug', $slug)->delete();
        return back()->with('success', 'Blog Posted and Deleted Successfully');
    }
}
