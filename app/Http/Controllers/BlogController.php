<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(){
    $blogCategories =BlogCategory::all();
       $blogs = Blog::with('blogCategory')->where('status', 'PUBLISHED')->orderBy('created_at', 'DESC')->paginate(10);

        return view('general.blog', ['blogs' => $blogs, 'blogCategories' => $blogCategories]);
    }

    public function show($slug){
        $blog = Blog::where('slug', $slug)->first();
        $blog->increment('views');
        $blogSuggestion = Blog::where('blog_category_id', $blog->blog_category_id)->take(3)->get();
        return view('general.show', [
            'blog' => $blog, 
            'suggestions' => $blogSuggestion
        ]);
    }

    
}
