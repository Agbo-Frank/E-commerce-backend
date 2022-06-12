<?php

namespace  App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\UtilityController as UtilityController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Password;

class AuthController extends UtilityController
{
    public function login(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => 'required|email:filter',
            'password' => 'required|min:6',
        ]);

        if($validated->fails()){
            return $this->responseHandler('Error!', null, $validated->errors(), 422);
        };

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyAuthApp', ['user'])->plainTextToken; 
            $success['user'] =  $user;
   
            return$this->responseHandler(
                'Logged in!', 
                $success, 
                false, 
                200
            );
        } 
        else{ 
            return $this->responseHandler(
                'incorrect password', 
                null, 
                ['password' => ['incorrect password']], 
                400
            );
        }
    }

    public function register(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => 'required|unique:users|email:filter',
            'password' => 'required|min:6',
        ]);
        
        if($validated->fails()){
            return $this->responseHandler(
                'Validation error', 
                null, 
                $validated->errors(), 
                422
            );
        }
        else{
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);

            User::create($input);

            return $this->responseHandler('User Registered', null, false, 200);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->responseHandler(
            'Logged out', 
            null, 
            false, 
            200
        );
    }

    public function forgetPassword(Request $request){
        $validated = Validator::make($request->all(), [
            'email' => 'required|email:filter',
        ]);
        
        if($validated->fails()){
            return $this->responseHandler(
                'Validation error', 
                null, 
                $validated->errors(), 
                422
            );
        }
        else{
            $status = Password::sendResetLink(
                $request->only('email')
            );

            return $status === Password::RESET_LINK_SENT
                ? back()->with(['status' => __($status)])
                : back()->withErrors(['email' => __($status)]);
        }
    }
}
