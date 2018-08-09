<?php

namespace App\Http\Controllers;

use Validator;
use Auth;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\DealerController as Dealer;
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
    public function __construct(Request $request, Dealer $dealer) {
        $this->request = $request;
        $this->dealer = $dealer;
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
    		'exp' => time() + 100*100 
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
    			'message' => 'Email does not exist.'
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
    		'message' => 'Email or password is wrong.'
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

        if ($this->request->role == 'sales') {
            $user->sales()->attach($this->request->dealer_id);
        }

        if ($this->request->role == 'dealer') {
            $dealer = $this->dealer->create($user);
        }


        $this->sendOTP($user);

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
          'message' => 'OTP not match.'
      ], 400);
    }

    public function sendOTP($user)
    {

        $apikey      = '3a5c0626a7f7375228e48591e8317238'; 
        $urlserver   = 'http://45.76.156.114/sms/api_sms_otp_send_json.php'; 
        $callbackurl = ''; 
        $senderid    = '0'; 
        $senddata = array(
            'apikey' => $apikey,  
            'callbackurl' => $callbackurl, 
            'senderid' => $senderid, 
            'datapacket'=>array()
        );

        $number=$user->phone;
        $message="Hi {$user->fullname}, OTP anda adalah {$user->challenge_code}";
        array_push($senddata['datapacket'],array(
            'number' => trim($number),
            'message' => $message
        ));

        $data=json_encode($senddata);
        $curlHandle = curl_init($urlserver);
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data))
        );
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 30);
        $respon = curl_exec($curlHandle);

        $http_code  = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        curl_close($curlHandle);


    }

}
