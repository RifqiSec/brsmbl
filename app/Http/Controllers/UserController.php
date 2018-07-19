<?php

namespace App\Http\Controllers;

use Validator;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
class UserController extends Controller 
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
    public function __construct(Request $request, User $user) {
        $this->request = $request;
        $this->user = $user;
    }

    public function index() {
        return [
            'status' => 'success',
            'data' => User::paginate(10)
        ];
    }

    public function show($id) {
        return [
            'status' => 'success',
            'data' => User::findOrFail($id)
        ];
    }

    public function update($id) {
        $user = $this->user->findOrFail($id);
        try {
            $user->update($this->request->only(['fullname', 'email', 'nik', 'noaccount', 'phone', 'photo', 'warning', 'token', 'role']));
        } catch (Exception $e) {
            return [
                'status' => 'failed',
                'message' => "Something error while updating user, please try again."
            ];
        }

        return [
            'status' => 'success',
            'data' => ''
        ];
    }

    public function destroy($id) {
        $user = $this->user->findOrFail($id);

        try {
            $user->delete();
        } catch (Exception $e) {
            return [
                'status' => 'failed',
                'message' => "Something error while deleting user, please try again."
            ];
        }

        return [
            'status' => 'success',
            'data' => ''
        ];
    }

    public function profile() {
        $user = $this->request->auth;
        return [
            'status' => 'success',
            'data' => $user
        ];
    }



}
