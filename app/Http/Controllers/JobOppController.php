<?php

namespace App\Http\Controllers;

use App\Models\Applications;
use App\Models\JobOpportunity;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use Throwable;
use App\Models\company;
use App\Models\User;
use App\Models\Application;
use App\Models\Favorite;

class JobOppController extends Controller
{
    public function create(Request $request)
    {
        $user = $request->user();
        $company = Company::where('user_id', $user['id'])->first();
        if ($company) {
            JobOpportunity::create([
                'company_id' => $company['id'],
                'opp_name' => $request['name'],
                'status' => 'open'
            ]);
            return response()->json([
                'status' => '200',
                'message' => 'success'
            ]);
        } else {
            return response()->json([
                'status' => '401',
                'message' => 'error'
            ]);
        }
    }
    public function edit(Request $request, $id)
    {
        $user = $request->user();
        $company = Company::where('user_id', $user['id'])->first();
        $job = JobOpportunity::find($id);
        if ($job && $company) {
            if ($company['id'] == $job['company_id']) {
                $job['opp_name'] = $request['name'];
                $job['status'] = $request['status'];
                $job->save();
                return response()->json([
                    'status' => '200',
                    'message' => 'success'
                ]);
            } else {
                return response()->json([
                    'status' => '401',
                    'message' => 'error'
                ]);
            }
        } else {
            return response()->json([
                'status' => '404',
                'message' => 'error'
            ]);
        }
    }
    public function showAll()
    {
        try {
            $alljobs = JobOpportunity::latest()->get();
            return response()->json([
                "status" => '200',
                "data" => $alljobs
            ]);
        } catch (Throwable $th) {
            return response()->json([
                "status" => '500',
                "data" => $th->getMessage()
            ]);
        }
    }
    public function showSingle($id)
    {
        $job = JobOpportunity::find($id);
        if ($job) {
            return response()->json([
                'status' => '200',
                'message' => 'success',
                'data' => $job
            ]);
        } else {
            return response()->json([
                'status' => '404',
                'message' => 'error'
            ]);
        }
    }
    public function apply(Request $request, $id)
    {
        $user = $request->user();
        if ($user['role'] == 'company') {
            return response()->json([
                'status' => '401',
                'message' => 'error'
            ]);
        }
        $job = JobOpportunity::find($id);
        if ($job) {
            if ($job['status'] == 'open') {
                Application::create([
                    'user_id' => $user['id'],
                    'job_id' => $job['id'],
                    'status' => 'waiting'
                ]);
                return response()->json([
                    'status' => '200',
                    'message' => 'success'
                ]);
            } else {
                return response()->json([
                    'status' => '200',
                    'message' => 'error'
                ]);
            }
        } else {
            return response()->json([
                'status' => '404',
                'message' => 'error'
            ]);
        }
    }
    public function cancel(Request $request, $id)
    {
        $user = $request->user();
        $job = JobOpportunity::find($id);
        if ($job) {
            $application = Application::where('user_id', $user['id'])
                ->where('job_id', $job['id'])->where('status', 'waiting')->first();
            if ($application) {
                $application->delete();
                return response()->json([
                    'status' => '200',
                    'message' => 'success'
                ]);
            } else {
                return response()->json([
                    'status' => '404',
                    'message' => 'error'
                ]);
            }
        }
    }
    public function showApplications(Request $request)
    {
        $user = $request->user();
        $myApllications = Application::where('user_id', $user['id'])->get();
        return response()->json([
            'status' => '200',
            'message' => 'success',
            'data' => $myApllications
        ]);
    }
    public function addFavorite(Request $request, $id)
    {
        $user = $request->user();
        $job = JobOpportunity::find($id);
        if ($job) {
            Favorite::create([
                'user_id' => $user['id'],
                'job_id' => $job['id']
            ]);
        }
    }
    public function removeFavorite(Request $request, $id)
    {
        $user = $request->user();
        $fav = Favorite::find($id);
        if ($fav) {
            if ($user['id'] == $fav['user_id']) {
                $fav->delete();
                return response()->json([
                    'status' => '200',
                    'message' => 'success'
                ]);
            } else {
                return response()->json([
                    'status' => '401',
                    'message' => 'error'
                ]);
            }
        } else {
            return response()->json([
                'status' => '404',
                'message' => 'error'
            ]);
        }
    }

}