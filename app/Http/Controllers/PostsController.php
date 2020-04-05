<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Post;

class PostsController extends Controller
{
    public function __construct() {
      $this->middleware('auth');
    }

    public function index() {
      // grab all users but get only the user_id
      $users = auth()->user()->following()->pluck('profiles.user_id');
      $posts = Post::whereIn('user_id', $users)->with('user')->latest()->paginate(5);
      return view('posts.index', compact('posts'));
    }

    public function create() {
      // create a posts directory folder to the views folde for organization of the files
      // return view('posts.create'); they are the same
      return view('posts/create');
    }

    public function store() {
     // validate our request
      $data = request()->validate([
        'caption' => 'required',
        'image' => ['required', 'image'],
      ]);


      // get image path to the system and file name
      $imagePath = request('image')->store('uploads', 'public');

      // create a instance of image then fit it width x height
      // fit to 1200 1200
      // resize will change the physical dimension of the image

      // image will be 1200 1200 no matter what.
      $image = Image::make(public_path("storage/{$imagePath}"))->fit(1200, 1200);

      // then save image
      $image->save();

      // get authenticated user id
      auth()->user()->posts()->create([
        'caption' => $data['caption'],
        'image' => $imagePath,
      ]);

      // then redirect
      return redirect('/profile/' . auth() ->user()->id);
    }

    public function show(\App\Post $post) {
      return view('posts/show', [
        'post' => $post
      ]);
    }
}
