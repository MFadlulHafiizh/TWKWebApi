<?php

namespace App\Http\Controllers;

use App\User;
use JWTAuth;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public $loginAfterSignUp = true;

    public function login(Request $request)
    {
        $credentials = $request->only("email","password");
        $token = null;
        $inputFcm = \DB::table('users')->where('email', $request->email)->update(['fcm_token' => $request->fcm_token]);
        $user = \DB::table('users')
        ->join('perusahaan', 'users.id_perusahaan', '=', 'perusahaan.id_perusahaan')
        ->where('email', $request->email)->first();
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                "status"=> false,
                "message" =>"Please enter your correct email or password"
            ]);
        }

        return response()->json([
            "status" => true,
            "message" => 'Login successfully',
            "token"  => $token,
            "user" => $user,
            "fcm" => $inputFcm
        ]);
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            "name"=> "required|string",
            "no_hp" => "required",
            "email"=> "required|email|unique:user",
            "password"=> "required|string|min:6|max:10"
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->no_hp = $request->no_hp;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        if ($this->$loginAfterSignUp) {
            return $this->login($request);
        }

        return response()->json([
            "status" =>true,
            "user" => $user
        ]);
    }

    public function deleteFcmToken($id){
        $findTokenFcm = User::find($id);
        if($findTokenFcm){
            $findTokenFcm->fcm_token = null;
            $findTokenFcm->save();
        }
        return response()->json($findTokenFcm);
    }

    public function logout(Request $request, $id)
    {
        $this ->validate($request, [
            "token"=>"required"
        ]);

        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                "status"=> true,
                "message"=> "User logged out successfully",
                "deleteFcm" => $this->deleteFcmToken($id)
            ]);
        } catch (JWTAuth $exception) {
            return response()->json([
                "status"=> false,
                "message"=> "Ops, the user can not be logged out"
            ]);
        }
        
    }

    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $exception) {

            return response()->json(['token_expired'], $exception->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $exception) {

            return response()->json(['token_invalid'], $exception->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $exception) {

            return response()->json(['token_absent'], $exception->getStatusCode());

        }

        return response()->json($user);
    }

}