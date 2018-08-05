<?php

namespace App\Http\Controllers;

use Validator;
use App\RequestTrx;
use App\Offer;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
class TransactionController extends Controller 
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
    public function __construct(Request $request, RequestTrx $requestTrx, Offer $offer) {
        $this->request = $request;
        $this->requestTrx = $requestTrx;
        $this->offer = $offer;
    }

    public function index() {
        return [
            'status' => 'success',
            'data' => $this->offer->with('request', 'request.vehicle.brand', 'sales', 'dealer')->whereHas('request', function($query) {
                $query->where('user_id', $this->request->auth->id);
            })->paginate(10)
        ];
    }

    public function show($id) {
        return [
            'status' => 'success',
            'data' => $this->dealer->findOrFail($id)
        ];
    }

}
