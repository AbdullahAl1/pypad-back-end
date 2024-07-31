<?php

namespace App\Http\Controllers;

use App\Models\Code;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CodeController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'filename' => [
                'required',
                'max:255',
                'string',
                Rule::unique('codes')->where(function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                })
            ],
            'code' => 'string',

        ]);

        $code = Code::create([
            'user_id' => $user->id,
            'filename' => $request->filename,
            'code' => $request->code,
        ]);
        return response()->json([
            "code" => $code
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $request->validate([
            'filename' => [
                'required',
                'max:255',
                'string',
                Rule::unique('codes')->where(function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                })->ignore($id),
            ],
            'code' => 'required|string',
        ]);

        // Find the code record by ID
        $code = Code::findOrFail($id);
        $code->code = $request->code;
        $code->filename = $request->filename;
        $code->save();

        return response()->json($code,204); // Return the updated record
    }

    public function showAll(Request $request)
    {
        $user = Auth::user();
        $codes = Code::where('user_id', $user->id)->get();
        if ($codes) {
            return response()->json($codes,200);
        } else {
            return response()->json(['message' => 'Code not found or you do not have access to this resource'], 404);
        }
    }

    public function showOne(Request $request,$id)
    {
        $user = Auth::user();
        $code = Code::where('id', $id)->where('user_id', $user->id)->first();
        
        if ($code) {
            return response()->json($code,200);
        } else {
            return response()->json(['message' => 'Code not found or you do not have access to this resource'], 404);
        }
    }

    public function destroy(Request $request, $id)
{
    $user = Auth::user();

    // Find the code by ID and ensure it belongs to the authenticated user
    $code = Code::where('id', $id)->where('user_id', $user->id)->first();

    if (!$code) {
        return response()->json(['message' => 'Code not found or you do not have access to this resource'], 404);
    }

    // Delete the code
    $code->delete();

    return response()->json(['message' => 'Code deleted successfully'], 200);
}
}
