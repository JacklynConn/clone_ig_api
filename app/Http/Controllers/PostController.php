<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index() /// get all posts
    {


        $posts = Post::with('user')->latest()->paginate(1000);
        foreach ($posts as $post) { /// loop through posts
            $post['likes_count'] = $post->likes->count(); /// add likes count to post
            $post['comments_count'] = $post->comments->count(); /// add comments count to post
            $post['liked'] = $post->likes->contains('user_id', auth()->user()->id); /// check if post is liked by logged in user
        }

        return response()->json([ /// return response as json
            "data" => $posts, /// add data to response
            "status" => "success" /// add status to response
        ]);
    }

    public function store(Request $request) /// create new post
    {

        $user = auth()->user(); /// get info of logged in user
        $data = $request->all(); /// get all data from request
        $data['user_id'] = $user->id; /// add user_id to data

        if ($request->hasFile('photo')) { /// check if request has photo
            $photo = $request->file('photo'); /// get photo from request
            $name = time() . '.' . $photo->getClientOriginalExtension(); /// generate name for photo
            $destinationPath = public_path('/post'); /// set destination path for photo
            $photo->move($destinationPath, $name); /// move photo to destination path
            $data['photo'] = $name; /// add photo name to data
        }

        $post = Post::create($data); /// create new post
        return response()->json([ /// return response as json
            "data" => $post, /// add data to response
            "status" => "success" /// add status to response
        ]);
    }

    public function update($id, Request $request)
    {
        $post = Post::find($id); /// find post by id
        if (!$post) { /// check if post not found
            return response([
                'message'   => 'Post not found',
                'status'    => 'error'
            ], 404); /// return response post not found
        }
        if ($post->user_id != auth()->user()->id) { /// check if post not belong to logged in user
            return response([
                'message'   => 'You can not edit this post'
                , 'status'  => 'error'
            ], 401); /// return response you can not edit this post
        }
        $data = $request->all(); /// get all data from request
        if ($request->hasFile('photo')) { /// check if request has photo
            $photo = $request->file('photo'); /// get photo from request
            $name = time() . '.' . $photo->getClientOriginalExtension(); /// generate name for photo
            $destinationPath = public_path('/post'); /// set destination path for photo
            $photo->move($destinationPath, $name); /// move photo to destination path
            $data['photo'] = $name; /// add photo name to data

            $oldPhoto = public_path('/post/') . $post->photo; /// get old photo path
            if (file_exists($oldPhoto)) { /// check if old photo exists inside folder
                @unlink($oldPhoto); /// delete old photo
            }
        }
        $post->update($data); /// update post
        return response()->json([ /// return response as json
            "data" => $post, /// add data to response
            "status" => "success" /// add status to response
        ]);
    }

    public function destroy($id)
    {
        $post = Post::find($id); /// find post by id
        if (!$post) { /// check if post not found
            return response([
                'message'   => 'Post not found',
                'status'    => 'error'
            ], 404); /// return response post not found
        }
        if ($post->user_id != auth()->user()->id) { /// check if post not belong to logged in user
            return response([
                'message'   => 'You can not delete this post'
                , 'status'  => 'error'
            ], 401); /// return response you can not delete this post
        }

        if ($post->photo) { /// check if post has photo
            $oldPhoto = public_path('/post/') . $post->photo; /// get old photo path
            if (file_exists($oldPhoto)) { /// check if old photo exists inside folder
                @unlink($oldPhoto); /// delete old photo
            }
        }
        $post->delete(); /// delete post
        return response()->json([ /// return response as json
            "message" => "Post deleted sucessfully", /// add message to response
            "status" => "success" /// add status to response
        ]);
    }
}