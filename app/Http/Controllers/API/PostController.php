<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Validator;
use DB;
class PostController extends Controller
{
   public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'posts' => Post::latest()->paginate(10)
        ], 200);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        DB::beginTransaction();
        Post::create($request->all());
        DB::commit();
        return response()->json(['success' => 'post Created Successfully'], 201);

    }


    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        $post = Post::find($request->id);
        $post->update($request->all());

        return response()->json(['success' => 'post Updated Successfully'], 200);
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        Post::destroy($id);

        return response()->json([
            'success' => 'post Removed Successfully',
        ]);
    }
}
