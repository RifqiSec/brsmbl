<?php

namespace App\Http\Controllers;

use Validator;
use App\Inbox;
use App\Payment;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
class PaymentController extends Controller 
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
    public function __construct(Request $request, Payment $payment) {
        $this->request = $request;
        $this->payment = $payment;
    }

    public function index() {
        return [
            'status' => 'success',
            'data' => $this->payment->where('user_id', $this->request->auth->id)->paginate(10),
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
 	