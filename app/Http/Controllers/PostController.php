<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return view('post.index',[
            'posts' => Post::take(5)->get(),
        ]);
    }
}
