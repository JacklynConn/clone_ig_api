<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // Register User
    public function register(Request $request)
    {
        $validateData = $request->validate([ // validate data from request
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed',
        ]);
        $validateData['password'] = bcrypt($request->password); // encrypt password
        if ($request->hasFile('profile')) { // check if request has photo
            $profile = $request->file('profile'); // get photo from request
            $name = time() . '.' . $profile->getClientOriginalExtension(); // generate name for photo
            $destinationPath = public_path('/profile'); // set destination path for photo
            $profile->move($destinationPath, $name); // move photo to destination path
            $validateData['profile_url'] = $name; // add photo name to data
        }
        $user = User::create($validateData); // create new user
        $accessToken = $user->createToken('authToken')->accessToken; // create access token
        return response(['user' => $user, 'access_token' => $accessToken], 201); // return response with user info and access token
    }

    // Login User
    public function login(Request $request)
    {
        $credentials = $request->validate([ // validate data from request
            'email' => 'email|required',
            'password' => 'required',
        ]);
        if (!auth()->attempt($credentials)) { // check if credentials not match
            return response(['message' => 'Invalid Credentials'], 401); // return response invalid credentials
        }
        $accessToken = auth()->user()->createToken('authToken')->accessToken; // create access token
        return response(['user' => auth()->user(), 'access_token' => $accessToken]); // return response with user info and access token
    }

    // Logout User
    public function logout()
    {
        auth()->logout(); // logout user
        return response(['message' => 'Logged out successfully']);
    }

    // Update User
    public function update(Request $request, $id)
    {
        $data = $request->all(); // get all data from request
        $user = User::find($id); // find user by id
        if (!$user) { // check if user not found
            return response(['message' => 'User not found'], 404); // return response user not found
        }

        if ($user) {  // check if user found
            if ($request->hasFile('profile')) { // check if request has photo
                $profile = $request->file('profile'); // get photo from request
                $name = time() . '.' . $profile->getClientOriginalExtension(); // generate name for photo
                $destinationPath = public_path('/profile'); // set destination path for photo
                $profile->move($destinationPath, $name); // move photo to destination path
                $data['profile_url'] = $name; // add photo name to data

                $oldImage = public_path('/profile/') . $user->profile_url; // get old image from folder
                if (file_exists($oldImage)) { // check if old image exists inside folder
                    @unlink($oldImage); // delete old image
                }
            }
            $user->update($data);
            return response(['user' => $user, 'message' => 'User updated successfully']);
        }
    }

    public function me(){
        return response(['user' => auth()->user()]);
    }
}
