<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function showPostDetail($postId){
        $post = Post::find($postId); // find post by id
        if (!$post) { // check if post not found
            return response([
                'message'   => 'Post not found',
                'status'    => 'error'
            ], 404); // return response post not found
        }
        $comments = $post->comments()->with('user')->latest()->get(); // get comments of post
        return response()->json([ // return response as json
            "comments" => $comments, // add data to response
            "status" => "success" // add status to response
        ]);
    }
    
    public function store(Request $request, $id){
        $user = auth()->user(); // get info of logged in user
        $post = Post::find($id); // find post by id
        if (!$post) { // check if post not found
            return response([
                'message'   => 'Post not found',
                'status'    => 'error'
            ], 404); // return response post not found
        }
        $data = $request->all(); // get all data from request
        $data['user_id'] = $user->id; // add user_id to data
        $comment = $post->comments()->create($data); // create new comment
        return response()->json([ // return response as json
            "data" => $comment, // add data to response
            "status" => "success" // add status to response
        ]);
    }

    public function update(Request $request, $id){
        $user = auth()->user(); // get info of logged in user
        $comment = Comment::find($id); // find comment by id
        if (!$comment) { // check if comment not found
            return response([
                'message' => 'Comment not found',
                'status' => 'error'
            ], 404); // return response comment not found
        }
        if ($user->id != $comment->user_id) { // check if user is authorized to edit comment
            return response([
                'message'   => 'You can not edit this comment', 
                'status'  => 'error'
            ], 401); // return response you can not edit this comment
        }
        $data = $request->all(); // get all data from request
        $comment->update($data); // update comment
        return response()->json([ // return response as json
            "comment" => $comment, // add data to response
            "status" => "success" // add status to response
        ]);
    }

    public function destroy($id){
        $comment = Comment::find($id); // find comment by id
        if (!$comment) { // check if comment not found
            return response([
                'message'   => 'Comment not found',
                'status'    => 'error'
            ], 404); // return response comment not found
        }
        $user = auth()->user(); // get info of logged in user
        if ($comment->user_id != $user->id) { // check if comment not belong to logged in user
            return response([
                'message'   => 'You can not delete this comment'
                , 'status'  => 'error'
            ], 401); // return response you can not delete this comment
        }
        $comment->delete(); // delete comment
        return response()->json([ // return response as json
            "status" => "success" // add status to response
        ]);
    }
}
