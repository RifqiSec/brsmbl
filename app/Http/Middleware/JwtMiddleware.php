<?php
namespace App\Http\Middleware;
use Closure;
use Exception;
use App\User;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
class JwtMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        $token = $request->header('token');
        
        if(!$token) {
            // Unauthorized response if token not there
            return response()->json([
                'status' => 'failed',
                'message' => 'Token not provided.'
            ], 401);
        }
        try {
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        } catch(ExpiredException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Provided token is expired.'
            ], 400);
        } catch(Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error while decoding token.'
            ], 400);
        }
        $user = User::find($credentials->sub);

        if ($user->is_confirm == 0) {

            $user->update(['challenge_code' => mt_rand(100000, 999999)]);

            //sending sms
            
            return response()->json([
                'status' => 'success',
                'data' => 'This account has not active yet, please insert OTP code.',
            ], 200);
        }

        $request->auth = $user;
        return $next($request);
    }
}