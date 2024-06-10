<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Jobs;
use Illuminate\Http\JsonResponse;

class jobsCompanyServices
{
    public function show($id): JsonResponse
    {
        $company = Company::where('admin', $_SERVER['PHP_AUTH_USER'])->first();

        if (is_null($company)) {
            return response()->json([
                'success' => false,
                'message' => "Company not found"
            ], 404);
        }

        $job = Jobs::where('id', $id)->first();

        if (is_null($job)) {
            return response()->json([
                'success' => false,
                'message' => "Job not found"
            ], 404);
        }

        return response()->json([
            'success' => true,
            'job' => $job,
        ]);
    }

    public function all(): JsonResponse
    {
        $company = Company::where('admin', $_SERVER['PHP_AUTH_USER'])->first();

        if (is_null($company)) {
            return response()->json([
                'success' => false,
                'message' => "Company not found"
            ], 404);
        }

        $allJobs = Jobs::all();

        if ($allJobs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => "Jobs not found"
            ], 404);
        }

        return response()->json([
            'success' => true,
            'jobs' => $allJobs,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $job = Jobs::where('id', $id)->first();

        if (is_null($job)) {
            return response()->json([
                'success' => false,
                'message' => "Job not found"
            ], 404);
        }

        $job->delete();

        return response()->json([
            'success' => true,
            'message' => "Job delete success"
        ]);
    }

    public function store($data): JsonResponse
    {
        $company = Company::where('admin', $_SERVER['PHP_AUTH_USER'])->first();

        if (is_null($company)) {
            return response()->json([
                'success' => false,
                'message' => "Company not authenticated"
            ], 401);
        }

        $job = Jobs::create([
            'company_id' => $company->id,
            'name' => $data['name'],
            'company_name' => $data['company_name'],
            'profession' => $data['profession'],
            'type' => $data['type'],
            'address' => $data['address'],
            'pay' => $data['pay'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'experience' => $data['experience'],
            'information' => $data['information'],
            'language' => $data['language'],
            'number' => $data['number'],
            'description' => $data['description'],
        ]);

        if (!$job->wasRecentlyCreated) {
            return response()->json([
                'success' => false,
                'message' => "Create job error"
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Job creation success",
            'id' => $job->id,
        ]);
    }

    public function update($data, $id): JsonResponse
    {
        $company = Company::where('admin', $_SERVER['PHP_AUTH_USER'])->first();

        if (is_null($company)) {
            return response()->json([
                'success' => false,
                'message' => "Company not authenticated"
            ], 401);
        }

        $job = Jobs::where('id', $id)->first();

        if (is_null($job)) {
            return response()->json([
                'success' => false,
                'message' => "job not found"
            ], 404);
        }

        $job->name = $data['name'];
        $job->company_name = $data['company_name'];
        $job->profession = $data['profession'];
        $job->type = $data['type'];
        $job->address = $data['address'];
        $job->pay = $data['pay'];
        $job->phone = $data['phone'];
        $job->email = $data['email'];
        $job->experience = $data['experience'];
        $job->information = $data['information'];
        $job->language = $data['language'];
        $job->number = $data['number'];
        $job->description = $data['description'];
        $saved = $job->save();

        if (!$saved) {
            return response()->json([
                'success' => false,
                'message' => "Update job error"
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Update job success",
            'id' => $job->id,
        ]);
    }
}
