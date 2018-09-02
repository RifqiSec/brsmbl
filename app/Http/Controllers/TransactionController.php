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
    public function __construct(Request $request, RequestTrx $requestTrx, Offer $offer, Dealer $dealer, User $user, Transaction $transaction) {
        $this->request = $request;
        $this->requestTrx = $requestTrx;
        $this->offer = $offer;
        $this->dealer = $dealer;
        $this->transaction = $transaction;
        $this->user = $user;
    }

    public function index() {

        if ($this->request->auth->role == 'sales') 
            return $this->salesRequestList();
        if ($this->request->auth->role == 'customer') 
            // return $this->userRequestList();
            return [
                'status' => 'success',
                'data' => $this->offer->with('request.vehicle.brand', 'sales', 'dealer')
                ->whereHas('request', function($query) {
                    $query->where('user_id', $this->request->auth->id);
                })->latest()->paginate(10)
            ];
        }

        public function userRequestList(){
            return [
                'status' => 'success',
                'data' => $this->requestTrx->with('vehicle.brand', 'offers')
                ->where('user_id', $this->request->auth->id)
                ->latest()
                ->paginate(10)
            ];
        }

        public function salesRequestList(){
            $request = $this->requestTrx
            ->whereHas('sales', function ($q) {
                $q->where('sales_id', $this->request->auth->id);
                $q->where('is_offered', 0);
            })
            ->with([
                'user'=>function($query){
                    $query->select('id','fullname', 'photo');
                }
            ])
            ->with('city')
            ->latest()
            ->paginate(10);

            return [
                'status' => 'success',
                'data' => $request
            ];
        }

        public function requestSales(){
           $request = $this->requestTrx->create([
                'user_id' => $this->request->auth->id,
                'vehicle_id' => $this->request->vehicleid,
                'city_id' => $this->request->cityid,
                'color' => $this->request->color,
                'qty' => $this->request->qty,
                'payment_method' => $this->request->payment_method,
                'dp' => ($this->request->dp !== '') ? $this->request->dp : '',
                'installment' => ($this->request->installment !== '') ? $this->request->installment : '',
            ]);

           return $this->selectSales($request->id);
        }

        public function offering(){
            $offer = $this->offer->create([
                'sales_id' => $this->request->auth->id,
                'request_id' => $this->request->requestid,
                'dealer_id' => $this->request->dealerid,
                'expired_date' => $this->request->expired_date,
                'tdp' => ($this->request->tdp !== '') ? $this->request->tdp : '',
                'installment' => ($this->request->installment !== '') ? $this->request->installment : '',
                'tenor' => ($this->request->tenor !== '') ? $this->request->tenor : '',
                'availibillity' => $this->request->availibillity,
                'promo' => $this->request->promo,
                'tnc' => $this->request->tnc,
                'description' => $this->request->description,
                'status' => 'offered',
                'installment' => ($this->request->installment !== '') ? $this->request->installment : '',
            ]);

            return [
                'status' => 'success',
                'data' => ''
            ]; 
        }

        //sales
        public function offerList($requestid)
        {
            $offer  = $this->offer->where(['request_id' => $requestid])->get();
            return [
                'status' => 'success',
                'data' => $offer
            ]; 

        }
         //sales
         public function successOfferList($requestid)
         {
             $offer  = $this->offer->where(['request_id' => $requestid, 'status' => 'success'])->with('sales')->get();
             return [
                 'status' => 'success',
                 'data' => $offer
             ]; 
 
         }

        //sales
        public function requestList()
        {
           return [
                'status' => 'success',
                'data' => $this->requestTrx->where('user_id', $this->request->auth->id)->withCount('offers as offering')->get()
           ];
        }

        //sales
        public function acceptOffer()
        {
            $offer = $this->offer->find($this->request->offerid);

            $offer->update(['status' => 'success']);

            $this->transaction->create([
                'offer_id' => $offer->id,
                'user_id' => $this->request->auth->id
            ]);

            return [
                'status' => 'success',
                'data' => $offer->sales
            ];
        }

        public function show($id) {
            return [
                'status' => 'success',
                'data' => $this->dealer->findOrFail($id)
            ];
        }

        public function salesList($area, $vehicle_id) {
            $sales = $this->dealer->where('city_id', $area)
            ->whereHas('vehicle', function ($q) use ($vehicle_id){
                $q->where('vehicles.id', $vehicle_id);
            })
            ->whereHas('sales', function ($q) {
                $q->where('token', '<=', 1);
            })
            ->with('sales')->latest()->paginate(10);

            return [
                'status' => 'success',
                'data' => $sales
            ];
        }

        public function selectSales($request) {
            $sales = array_map('intval', explode(',', $this->request->sales_id));

            if($this->user->find($this->request->auth->id)->token <= count($sales)) {
                return [
                    'status' => 'failed',
                    'message' => 'Token tidak cukup'
                ];
            }

            collect($sales)->map(function($item) use ($request){
                \DB::table('selected_sales')
                ->insert(['request_id' => $request, 'sales_id' => $item]);
            });

            $this->user->find($this->request->auth->id)->update(['token' => \DB::raw('token-'.count($sales))]);

            return [
                'status' => 'success',
                'message' => 'Sedang di proses'
            ];
        }

        public function saveOrder() {
        // going to be save request and brodcast to sales by function on declare before 

            $offer = $this->request->offerid;
            $user = $this->request->auth->id;

            
        }

    }
