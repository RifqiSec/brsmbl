<?php

namespace App\Http\Controllers;

use Validator;
use App\Inbox;
use App\User;
use App\TokenHistory;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
class TokenController extends Controller 
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
    public function __construct(Request $request, Inbox $inbox, User $user, TokenHistory $tokenHistory) {
        $this->request = $request;
        $this->inbox = $inbox;
        $this->user = $user;
        $this->tokenHistory = $tokenHistory;
    }

    public function index() {
        return [
            'status' => 'success',
            'data' => $this->tokenHistory->where('user_id', $this->request->auth->id)->paginate(10),
        ];
    }

    public function create() {
        $data = [
            'from' => $this->request->auth->id,
            'to' => $this->request->to,
            'type' => $this->request->type,
            'title' => $this->request->title,
            'message' => $this->request->message,
        ];

        $this->inbox->create($data);

        return [
            'status' => 'success',
            'message' => 'Message sent!.'
        ];
    }
}
    