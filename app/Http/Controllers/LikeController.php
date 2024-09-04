<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function getLikes($postId)
    {
        $post = Like::where('post_id', $postId)->with('user')->get(); // get likes of post
        $user = $post->pluck('user'); // get users of likes
        return response()->json([ // return response as json
            "data" => $user, // add data to response
            "status" => "success" // add status to response
        ]);
    }

    // like and dislike post
    public function toggleLike($postId)
    {
        $user = auth()->user(); // get info of logged in user
        $post = Post::find($postId); // find post by id
        $liked = $post->likes->contains('user_id', $user->id); // check if post is liked by logged in user
        if ($liked) { // check if post is liked by logged in user
            $post->likes()->where('user_id', $user->id)->delete(); // delete like
        } else {
            $post->likes()->create([
                'user_id' => $user->id,
                'post_id' => $postId,
            ]); // create like
        }
        return response()->json([ // return response as json
            "status" => "success" // add status to response
        ]);
    }
}
