<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServerController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'country' => 'required|string',
                'username' => 'required|string',
                'password' => 'required|string',
                'config' => 'string|nullable',
                'image' => 'string|nullable',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $server = new Server([
                'country' => $request->input('country'),
                'username' => $request->input('username'),
                'password' => $request->input('password'),
                'config' => $request->input('config'),
                'image' => $request->input('image'),
                'isPremium' => $request->input('isPremium', false),
            ]);

            $server->save();

            return response()->json(['message' => 'Server created successfully','server'=>$server], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server creation failed'], 500);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $server = Server::find($id);

            if (!$server) {
                return response()->json(['error' => 'Server not found'], 404);
            }

            $server->country = $request->input('country', $server->country);
            $server->username = $request->input('username', $server->username);
            $server->password = $request->input('password', $server->password);
            $server->config = $request->input('config', $server->config);
            $server->image = $request->input('image'); 
            $server->isPremium = $request->input('isPremium') ? $request->input('isPremium') :$server->isPremium;
            $server->save();

            return response()->json(['message' => 'Server updated successfully','server'=>$server], 200);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Server update failed'], 500);
        }
    }
    public function destroy($id)
    {
        try {
            $server = Server::find($id);

            if (!$server) {
                return response()->json(['error' => 'Server not found'], 404);
            }

            $server->delete();

            return response()->json(['message' => 'Server deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server deletion failed'], 500);
        }
    }
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('paginate', 10);

            $servers = Server::paginate($perPage);

            return response()->json($servers, 200);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Server list retrieval failed'], 500);
        }
    }
    public function show($id)
    {
        try {
            $server = Server::find($id);

            if (!$server) {
                return response()->json(['error' => 'Server not found'], 404);
            }

            return response()->json($server, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server retrieval failed'], 500);
        }
    }
}
