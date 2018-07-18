<?php

namespace App\Http\Controllers;

use Validator;
use Auth;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller 
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request) {
    	$this->request = $request;
    }
    /**
     * Create a new token.
     * 
     * @param  \App\User   $user
     * @return string
     */
    protected function jwt(User $user) {
    	$payload = [
    		'iss' => "lumen-jwt", 
    		'sub' => $user->id, 
    		'iat' => time(), 
    		'exp' => time() + 60*60 
    	];

    	return JWT::encode($payload, env('JWT_SECRET'));
    } 
    /**
     * Authenticate a user and return the token if the provided credentials are correct.
     * 
     * @param  \App\User   $user 
     * @return mixed
     */
    public function authenticate(User $user) {
    	$this->validate($this->request, [
    		'email'     => 'required|email',
    		'password'  => 'required'
    	]);
        // Find the user by email
    	$user = User::where('email', $this->request->input('email'))->first();
    	if (!$user) {
    		return response()->json([
    			'status' => 'failed',
    			'error' => 'Email does not exist.'
    		], 400);
    	}
        // Verify the password and generate the token
    	if (Hash::check($this->request->input('password'), $user->password)) {
    		return response()->json([
    			'status' => 'success',
    			'token' => $this->jwt($user)
    		], 200);
    	}
        // Bad Request response
    	return response()->json([
    		'status' => 'failed',
    		'error' => 'Email or password is wrong.'
    	], 400);
    }

    public function register() {
    	$this->validate($this->request, [
    		'email'     => 'required|email',
    		'password'  => 'required|confirmed',
    		'fullname'	=> 'required',
    		'phone'		=> 'required',
    		'role'		=> 'required',
    	]);


    	$user = User::create([
    		'email' => $this->request->email,
    		'password' => Hash::make($this->request->password),
    		'fullname' => $this->request->fullname,
    		'phone' => $this->request->phone,
    		'role' => $this->request->role,
    		'noaccount' => '-',
    		'challenge_code' => mt_rand(100000, 999999)
    	]);

    	//Send sms otp

    	if($user) {
    		return response()->json([
    			'status' => 'success',
    			'token' => $this->jwt($user)
    		], 200);
    	}
    }

    public function validateOTP(){
    	if ($this->request->auth->challenge_code == $this->request->otp) {

    		$this->request->auth->update(['is_confirm' => 1]);
    		return response()->json([
    			'status' => 'success'
    		], 200);
    	}

    	return response()->json([
    		'status' => 'failed',
    		'error' => 'OTP not match.'
    	], 400);
    }

}
