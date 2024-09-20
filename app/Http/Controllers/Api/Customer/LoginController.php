<?php

namespace App\Http\Controllers\Api\Customer;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{    
    public function index(Request $request)
    {
        //set validasi
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');
        if(!$token = auth()->guard('api_customer')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email or Password is incorrect'
            ], 401);

        }
        
        return response()->json([
            'success' => true,
            'user'    => auth()->guard('api_customer')->user(),  
            'token'   => $token   
        ], 200);
    }

    public function getUser()
    {
        return response()->json([
            'success' => true,
            'user'    => auth()->guard('api_customer')->user()
        ], 200);
    }
    
    public function refreshToken(Request $request)
    {
        $refreshToken = JWTAuth::refresh(JWTAuth::getToken());
        $user = JWTAuth::setToken($refreshToken)->toUser();
        $request->headers->set('Authorization','Bearer '.$refreshToken);

        return response()->json([
            'success' => true,
            'user'    => $user,
            'token'   => $refreshToken,  
        ], 200);
    }
    
    public function logout()
    {
        $removeToken = JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json([
            'success' => true,
        ], 200);

    }
}