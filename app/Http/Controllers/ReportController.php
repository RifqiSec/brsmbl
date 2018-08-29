<?php

namespace App\Http\Controllers;

use Validator;
use App\RequestTrx;
use App\Offer;
use App\Dealer;
use App\User;
use App\Transaction;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
class ReportController extends Controller 
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
    public function __construct(Request $request, RequestTrx $requestTrx, Offer $offer, Dealer $dealer, User $user, Transaction $transaction) {
        $this->request = $request;
        $this->requestTrx = $requestTrx;
        $this->offer = $offer;
        $this->dealer = $dealer;
        $this->transaction = $transaction;
        $this->user = $user;
    }
    
    public function sales() {
        $dealer = $this->dealer->where('user_id', $this->request->auth->id)->with('sales')->first();
        $sort = ($this->request->has('sort')) ? '' : '' ;
        $from = ($this->request->has('from')) ? '' : '' ;
        $to = ($this->request->has('to')) ? '' : '' ;
        $year = ($this->request->has('year')) ? '' : '' ;

        $report =  $dealer->sales->map(function($item) {

            $request = $this->requestTrx->where('user_id', $item->id)->count();
            $offer = $this->offer->where('sales_id', $item->id)->count();
            $success = $this->offer->has('transaction')->where('sales_id', $item->id)->count();

            return [
              'id' => $item->id,
              'fullname' => $item->fullname,
              'phone' => $item->phone,
              'nik' => $item->nik,
              'token' => $item->token,
              'request' => $request,
              'offer' => $offer,
              'success' => $success
            ];
        });

        return [
            'status' => 'success',
            'data' => $report
        ]; 
    }

}
