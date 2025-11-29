<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\CompanyInfo;  
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role; 

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($validatedData)) {
            $user = Auth::user();
            $customToken = $this->createCustomToken($user->id, 'mysecretkey');
            $user->api_token = $customToken;
            $user->save();

            $companyinfo = CompanyInfo::where('id',$user->comp_id)->get()->toArray();
            $userinfo = Auth::user()->toArray();


            return response()->json(['token' => $customToken,'companyid'=>$user->company_id,'companyinfo'=>$companyinfo,'user'=>$userinfo], 200);
        } else {
            return response()->json(['error' => 'UnAuthorised'], 401);
        }
    }

    public function createCustomToken($userId, $secretKey)
    {
        $timestamp = time();
        $tokenData = $userId . $timestamp;
        $customToken = hash_hmac('sha256', $tokenData, $secretKey);
        return $customToken;
    }
}
