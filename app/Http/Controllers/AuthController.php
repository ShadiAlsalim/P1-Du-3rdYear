<?php

namespace App\Http\Controllers;

use App\Models\company;
use App\Models\employee;
use App\Models\User;
use App\Notifications\EmailValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required| string| min:8| regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
                    'role' => 'required',

                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if ($request->role == "employee" || $request->role == "company")
                $user = User::create([
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => $request->role,
                    'followers' => 0
                ]);

            if ($request->role == "employee") {
                employee::create([
                    'user_id' => $user->id,
                ]);
            } else if ($request->role == "company") {
                company::create([
                    'user_id' => $user->id,
                ]);
            }

            $user->generateCode();

            $user->notify(new EmailValidator());

            return response()->json([
                'status' => '200',
                'message' => 'User Created Successfully , check your email',
                'user-info' => $user,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }

    }


    public function verifyEmail(Request $request)
    {

        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = User::where('code', $request->code)->first();
        if (!$user) {
            return response()->json([
                'message' => 'Wrong code '
            ]);
        }

        $token = $user->createToken("apiToken")->plainTextToken;

        return response()->json([
            'status' => '200',
            'message' => ' successfully',
            'user' => $user,
            'token' => $token
        ]);
    }


    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            $role = $user->role;
            if ($role == "employee")
                $token = $user->createToken("employee_token")->plainTextToken;
            else
                $token = $user->createToken("company_token")->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $token,
                'user' => $user->role
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function forgetPassword(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|string|email',
                ]
            );


            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'message' => 'Wrong email'
                ]);
            }

            $user->generateCode();

            $user->notify(new EmailValidator());

            return response()->json([
                'status' => '200',
                'message' => 'we sent a code to your email',
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function resetPassword(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'password' => 'required| string| min:8| regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'message' => 'Wrong email'
                ]);
            }

            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status' => '200',
                'message' => 'success',
                'user_inft' => $user,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function resendCode(Request $request)
    {

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'Wrong email'
            ]);
        }
        $user->generateCode();
        $user->notify(new EmailValidator());
        return response()->json([
            'status' => '200',
            'message' => 'we resend the code',
        ]);
    }


    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        return response()->json([
            'message' => "logout sucsses"
        ]);
    }

}