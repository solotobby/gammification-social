<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(){
        $blogs = Blog::where('status', 'PUBLISHED')->paginate(10);
        
        return view('blog.index', ['blogs' => $blogs]);
    }

    public function show($slug){
        $blog = Blog::where('slug', $slug)->first();
        $blog->increment('views');
        $blogSuggestion = Blog::where('blog_category_id', $blog->blog_category_id)->take(3)->get();
        return view('blog.show', ['blog' => $blog, 'suggestions' => $blogSuggestion]);
    }

    
}
