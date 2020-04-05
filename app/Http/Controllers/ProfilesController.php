<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Cache;

class ProfilesController extends Controller
{

  public function index(User $user)
  {
      // check if its following
      $follows = (auth()->user()) ? auth()->user()->following->contains($user->id) : false;

      $postCount = Cache::remember(
        'count.post.' . $user->id, 
        now()->addSeconds(30), 
        function() use ($user) {
          return $user->posts->count();
        }
      );

      $followersCount = Cache::remember(
        'count.followers.' . $user->id, 
        now()->addSeconds(30), 
        function() use ($user) {
          return $user->profile->followers->count();
        }
      );

      $followingCount = Cache::remember(
        'count.following.' . $user->id, 
        now()->addSeconds(30), 
        function() use ($user) {
          return $user->following->count();
        }
      );

      return view('profiles/index', compact('user', 'follows', 'postCount', 'followersCount', 'followingCount'));
  }

  public function edit(User $user) {
    $this->authorize('update', $user->profile);

    return view('profiles.edit', compact('user'));
  }

  public function update(User $user) {
    $this->authorize('update', $user->profile);

    $data = request()->validate([
      'title' => 'required',
      'description' => 'required',
      'url' => 'url',
      'image' => ''
    ]);

    // update image if the user has uploaded one
    if(request('image')) {
      $imagePath = request('image')->store('profile', 'public');
      $image = Image::make(public_path("storage/{$imagePath}"))->fit(1000, 1000);
      $image->save();

      // set this here so if image is  uploaded, then declare the image path
      $imageArray = ['image' => $imagePath];
    }

    // we use array_merge to overwrite the image and use the path instead 
    auth()->user()->profile->update(array_merge(
      $data,
      // and here if no imageArray is declare it wont overwrite the default
      $imageArray ?? []
    ));


    return redirect("/profile/{$user->id}");
  }
}
