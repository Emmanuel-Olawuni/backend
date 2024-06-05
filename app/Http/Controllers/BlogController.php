<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Blog::all();
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required | string | max:255',
            'description' => 'required | string ',
            'content' => 'required | string  ',
            'thumbnail' => ' image ',
            'mainImage' => ' image ',
            'images' => ' array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $blog = new Blog($request->only('title', 'description', 'content'));
        if ($request->hasFile('thumbnail')) {
            $blog->thumbnail = $request->file('thumbnail')->store('thumbnail');
        }
        if ($request->hasFile('mainImage')) {
            $blog->mainImage = $request->file('mainImage')->store('mainImage');
        }
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as  $image) {
                $images[] = $image->store('images');
                $blog->images = json_encode($images);
            }
        }
        $blog->save();
        return response()->json([
            'message' => 'Blog uploaded successfully '
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($blog)
    {
        $data = Blog::find($blog);
        if ($data) {

            return  response()->json($data, 200);
        } else {
            return response()->json([
                'error' => 'Blog Not Found'
            ], 404);
        }
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $blog)
    {
        $blog = Blog::find($blog);
        $validator = Validator::make($request->all(), [
            'title' => ' string | max:255',
            'description' => '  string ',
            'content' => '  string  ',
            'thumbnail' => 'image|mimes:jpeg,png,jpg,gif ',
            'mainImage' => 'image |mimes:jpeg,png,jpg,gif ',
            'images' => ' array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if ($blog) {
            $blog->title = $request->title;
            $blog->description = $request->description;
            $blog->content = $request->content;
            if ($request->hasFile('thumbnail')) {
                $blog->thumbnail = $request->file('thumbnail')->store('thumbnail');
            }
            if ($request->hasFile('mainImage')) {
                $blog->mainImage = $request->file('mainImage')->store('mainImage');
            }
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as  $image) {
                    $path = $image->store('images');
                    $blog->images()->create(['path' => $path]);
                }
            }
            $blog->save();

            return response()->json([
                'message' => 'Blog updated successfully '
            ], 200);
        } else {

            return response()->json([
                'error' => 'Blog Not Found'
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy($blog)
    {
        $data = Blog::find($blog);
        $data->delete();
        return response()->json([
            'message' => 'Blog deleted Successfully'
        ], 200);
    }
}
