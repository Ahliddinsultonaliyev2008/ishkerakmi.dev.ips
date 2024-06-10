<?php

namespace App\Services;

use App\Models\Resume;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class ResumeServices
{
    function show($id): JsonResponse

    {

        $user_id = auth('sanctum')->user()->id;
        $user = User::where('id', $user_id)->first();

        if (is_null($user))
            return response()->json([
                'success' => false,
                'massage' => "User not found"
            ], 404);

        $resume = Resume::where('id', $id) ->with('comments') ->first();

        if (is_null($resume))
            return response()->json([
                'success' => false,
                'massage' => "Resume not found"
            ], 404);


        return response()->json([
            'success' => true,
            'resume' => $resume,
        ]);
    }
    function all(): JsonResponse

    {

        $user_id = auth('sanctum')->user()->id;
        $user = User::where('id', $user_id)->first();

        if (is_null($user))
            return response()->json([
                'success' => false,
                'massage' => "User not found"
            ], 404);

        $resumes = Resume::all();

        if (empty($resumes))
            return response()->json([
                'success' => false,
                'massage' => "Resume not found"
            ], 404);


        return response()->json([
            'success' => true,
            'resumes' => $resumes,
        ]);
    }
    function destroy($id): JsonResponse

    {
        $user_id = auth('sanctum')->user()->id;
        $user = User::where('id', $user_id)->first();

        if (is_null($user))
            return response()->json([
                'success' => false,
                'massage' => "User not found"
            ], 404);

        $resume = Resume:: where('id', $id)->first();

        if (is_null($resume))
            return response()->json([
                'success' => false,
                'massage' => "Resume not found"
            ], 404);

        $resume->delete();

        return response()->json([
            'success' => true,
            'massage' => "Resume delete success"
        ]);
    }
    function store($data): JsonResponse
    {
        $user_id = auth('sanctum')->user()->id;
        $user = User::where('id', $user_id)->first();

        if (is_null($user))
            return response()->json([
                'success' => false,
                'massage' => "User not found"
            ], 404);

        $resume = Resume:: create([
            'user_id' => $user_id,
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'profession' => $data['profession'],
            'title' => $data['title'],
            'address' => $data['address'],
            'type' => $data['type'],
            'gender' => $data['gender'],
            'experience' => $data['experience'],
            'pay' => $data['pay'],
            'phone' => $data['phone'],
            'description' => $data['description'],
        ]);
        if (!$resume->exists())
            return response()->json([
                'success' => false,
                'massage' => "Create resume error"
            ], 500);


        return response()->json([
            'success' => true,
            'massage' => "Resume creation success",
            'id' => $resume,
        ]);
    }
    function update($data, $id): JsonResponse
    {
        $user_id = auth('sanctum')->user()->id;
        $user = User::where('id', $user_id)->first();

        if (is_null($user))
            return response()->json([
                'success' => false,
                'massage' => "User not found"
            ], 404);

        $resume = Resume:: where('id', $id)->first();

        if (is_null($resume))
            return response()->json([
                'success' => false,
                'massage' => "Resume not found"
            ], 404);


        $resume->user_id = $user_id;
        $resume->firstname = $data['firstname'];
        $resume->lastname = $data['lastname'];
        $resume->profession = $data['profession'];
        $resume->title = $data['title'];
        $resume->address = $data['address'];
        $resume->type = $data['type'];
        $resume->gender = $data['gender'];
        $resume->experience = $data['experience'];
        $resume->pay = $data['pay'];
        $resume->phone = $data['phone'];
        $resume->description = $data['description'];
        $saved = $resume->save();

        if (!$saved)
            return response()->json([
                'success' => false,
                'massage' => "Update resume error"
            ], 500);


        return response()->json([
            'success' => true,
            'massage' => "Update resume success",
            'id' => $resume->id,
        ]);
    }
}
