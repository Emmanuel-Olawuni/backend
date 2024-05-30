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
    public function store(Request $request) //store a blog to database. Validation first
    {
        $validator = Validator::make($request->alll(), [
            'title' => 'required | string | max:255',
            'description' => 'required | string ',
            'content' => 'required | string  ',
            'thumbnail' => 'nullable | image|mimes:jpeg,png,jpg,gif ',
            'main_image' => 'nullable | image |mimes:jpeg,png,jpg,gif ',
            'images' => 'nullabe| array',
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
            $blog->thumbnail = $request->file('mainImage')->store('mainImage');
        }
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as  $image) {
                $path = $image->store('images');
                $blog->images()->create(['path' => $path]);
            }
        }
        $blog->save();
        return response()->json([
            'message' => 'Blog uploaded successfully '
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        return Blog::findOrFail($blog);
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blog $blog)
    {
        $blog = Blog::findOrFail($blog);
        $validator = Validator::make($request->alll(), [
            'title' => 'sometimes |required | string | max:255',
            'description' => ' sometimes | required | string ',
            'content' => 'sometimes | required | string  ',
            'thumbnail' => 'nullable | image|mimes:jpeg,png,jpg,gif ',
            'main_image' => 'nullable | image |mimes:jpeg,png,jpg,gif ',
            'images' => 'nullabe| array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $blog->fill($request->only('title', 'description', 'content'));
        if ($request->hasFile('thumbnail')) {
            $blog->thumbnail = $request->file('thumbnail')->store('thumbnail');
        }
        if ($request->hasFile('mainImage')) {
            $blog->thumbnail = $request->file('mainImage')->store('mainImage');
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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        $data = Blog::findOrFail($blog);
        Storage::delete($data->thumbnail);
        Storage::delete($data->mainImage);
        foreach ($blog->images as $image) {
            Storage::delete($image->path);
            $image->delete();
        }
        $data->delete();
        return response()->json([
            'message' => 'Blog deleted Successfully'
        ], 200);
    }
}
