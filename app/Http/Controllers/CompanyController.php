<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\company;
use App\Models\Follow;
use App\Models\JobOpportunity;
use App\Models\User;

class CompanyController extends Controller
{
    public function showAll()
    {
        $companies = company::get();
        return response()->json([
            'status' => '200',
            'message' => 'success',
            'data' => $companies
        ]);
    }
    public function showSingle($id)
    {
        $company = company::find($id);
        if ($company) {
            $jobs = JobOpportunity::where('company_id', $id)->get();
            return response()->json([
                'status' => '200',
                'message' => 'success',
                'data' => $jobs
            ]);
        } else {
            return response()->json([
                'status' => '404',
                'message' => 'error'
            ]);
        }
    }
    public function addFollow(Request $request, $id)
    {
        $user = $request->user();
        if ($user['id'] == $id) {
            return response()->json([
                'status' => '200',
                'message' => 'error'
            ]);
        } else {
            Follow::create([
                'follower_id' => $user['id'],
                'followed_id' => $id
            ]);

            $followed = User::find($id);
            $followed['followers'] = $followed['followers'] + 1;
            $followed->save();
        }
    }
    public function removeFollow(Request $request, $id)
    {
        $user = $request->user();
        $company = User::find($id);
        $follow = Follow::where('follower_id', $user['id'])->where('followed_id', $company['id']);
        if ($follow) {

            $follow->delete();

            $followed = User::find($id);
            $followed['followers'] = $followed['followers'] - 1;
            $followed->save();

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