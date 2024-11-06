<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserManagement\CreateUserRequest;
use App\Http\Requests\Admin\UserManagement\UpdateUserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                ], 404);
            }
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'detail_address' => $request->detail_address,
                'city' => $request->detail_address,
                'country' => $request->country,
                'pastal_code' => $request->pas_code,
                'password' => Hash::make($request->password),
            ]);
            return response()->json([
                'message' => 'successfully',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                ], 404);
            }
            $user->delete();
            return response()->json([
                'message' => 'successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function index()
    {
        try {
            $page = request()->get('page', 1);
            $limit = request()->get('limit', 10);
            $q = request()->get('q', null);
            $city = request()->get('city', null);
            $country = request()->get('country', null);
            $users = User::query();
            $users->when($q, function ($query, $q) {
                return $query->where('name', 'like', '%' . $q . '%')
                    ->orWhere('email', 'like', '%' . $q . '%');
            });
            $users->when($q, function ($query, $city){
                return $query->where("city", $city);
            });
            $users->when($q, function ($query, $country){
                return $query->where("country", $country);
            });
            $users->paginate($limit, ['*'], 'page', $page);
            return response()->json([
                'message' => 'successfully',
                'data' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'total_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total_data' => $users->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
        return response()->json([
            'message' => 'successfully',
            'data' => $user,
        ], 200);
    }
}
