<?php

namespace App\Http\Controllers;

use Validator;
use App\RequestTrx;
use App\Offer;
use App\Dealer;
use App\User;
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
    public function __construct(Request $request, RequestTrx $requestTrx, Offer $offer, Dealer $dealer, User $user) {
        $this->request = $request;
        $this->requestTrx = $requestTrx;
        $this->offer = $offer;
        $this->dealer = $dealer;
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

        public function offering()
        {
        # code...
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

        public function selectSales() {
            $sales = $this->request->sales_id;
            $request = $this->request->request_id;

            if($this->user->find($this->request->auth->id)->token < count($sales)) {
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
        }

    }
