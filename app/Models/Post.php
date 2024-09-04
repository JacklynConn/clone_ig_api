<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable=[
        'caption',
        'photo',
        'user_id',
    ];

    /// create date and time now
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s', 
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function user(){
        return $this->belongsTo(User::class); // get user info of post
    }

    public function likes(){
        return $this->hasMany(Like::class); // get likes of post 
    }

    public function comments(){
        return $this->hasMany(Comment::class); // get comments of post
    }



    
}
