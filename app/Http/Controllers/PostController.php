<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use Image;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->with(['user', 'likes'])->paginate(5);
     
        return view('posts.index', [
            'posts'=>$posts
        ]);
    }

    public function store(Request $request){

        $validatedData = $this->validate($request, [
            'body' => 'required',
            'image' => 'sometimes|image',
        ]);
        
        $post = $request->user()->posts()->create($validatedData); 
       

        if ($request->hasfile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $location = storage_path('app/public/images/') . $filename;
    
            Image::make($image)->crop(240, 240)->save($location);
    
            $post->image = $filename;
           
          }
          $post->save();
          
        
        return back();
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();
        return back();
    }

}
