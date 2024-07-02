<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $user = new User([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->save();
            $token = $user->createToken('user_token')->plainTextToken;

            return response()->json([
                'massage'=>'User Signed Up',
                'token' => $token
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Registration failed'], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $user = User::where(['email' => $request->email])->first();


            if ($user && Hash::check($request->password, $user->password)) {
                $token = $user->createToken('user_token');

                return response()->json([
                    'message' => 'You have logged in successfully',
                    'token' => $token->plainTextToken
            ], 200);
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Login failed'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }


            $validator = Validator::make($request->all(), [
                'name' => 'string',
                'email' => 'email|unique:users,email,' . $id,
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->subscription_id = $request->input('subscription_id') ? $request->input('subscription_id') :$user->subscription_id;
            $user->isPremium = $request->input('isPremium') ? $request->input('isPremium') :$user->isPremium;
            $user->isSuspended = $request->input('isSuspended') ? $request->input('isSuspended') :$user->isSuspended;
            $user->save();

            return response()->json(['message' => 'User updated successfully','user'=>$user], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Update failed'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $user->delete();

            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Deletion failed'], 500);
        }
    }


    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $user = Auth::user();
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['error' => 'Current password is incorrect'], 401);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json(['message' => 'Password changed successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Password change failed'], 500);
        }
    }
    public function logout()
    {
        try {
            Auth::guard('api_users')->user()->tokens()->delete();

            return response()->json(['message' => 'Logout successful'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Logout failed'], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $perPage = $request->input('paginate', 10);

            $users = User::paginate($perPage);
            return response()->json($users, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'User list retrieval failed'], 500);
        }
    }
    public function show($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            return response()->json($user, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'User retrieval failed'], 500);
        }
    }
}
