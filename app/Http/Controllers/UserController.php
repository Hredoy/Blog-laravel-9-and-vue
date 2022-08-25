<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'users' => User::latest()->paginate(10)
        ], 200);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        DB::beginTransaction();
        $request['password'] = bcrypt($request->password);
        User::create($request->all());
        DB::commit();
        return response()->json(['success' => 'User Created Successfully'], 201);

    }


    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $user = User::find($request->id);
        $request['password'] = bcrypt($request->password);
        $user->update($request->all());

        return response()->json(['success' => 'User Updated Successfully'], 200);
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        User::destroy($id);

        return response()->json([
            'success' => 'User Removed Successfully',
        ]);
    }

}
