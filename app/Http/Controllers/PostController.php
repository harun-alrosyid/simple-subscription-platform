<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreatePostRequest;
use App\Models\Website;
use App\Models\Post;

class PostController extends Controller
{
    public function store(CreatePostRequest $req, Website $website)
    {
        $post = $website->posts()->create($req->validated());
        return response()->json(['message'=>'Post created','data'=>$post], 201);
    }
}
