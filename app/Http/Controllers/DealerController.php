<?php

namespace App\Http\Controllers;

use Validator;
use App\Dealer;
use App\User;
use App\Vehicle;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
class DealerController extends Controller 
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
    public function __construct(Request $request, Dealer $dealer, Vehicle $vehicle, User $user) {
        $this->request = $request;
        $this->dealer = $dealer;
        $this->user = $user;
        $this->vehicle = $vehicle;
    }

    public function index() {
        return [
            'status' => 'success',
            'data' => $this->dealer->latest()->paginate(10)
        ];
    }

    public function show($id) {
        return [
            'status' => 'success',
            'data' => $this->dealer->with('user', 'city')->findOrFail($id)
        ];
    }

    public function vehicleList(){
        return [
            'status' => 'success',
            'data' => $this->vehicle->whereHas('dealer', function($q) {
                $q->where('user_id', $this->request->auth->id);
            })->latest()->paginate(10)
        ];
        
    }

    public function addVehicle(){
        $dealer = $this->dealer->where('user_id', $this->request->auth->id)->first();
        $vehicleid = explode(",", $this->request->vehicleid);
        collect($vehicleid)->map(function($item) use ($dealer){
            $dealer->vehicle()->attach($item);
        });
        return [
            'status' => 'success',
            'data' => ''
        ];
    }

    public function salesList(){
        $dealerId = $this->dealer->where('user_id', $this->request->auth->id)->first()->id;
        return [
            'status' => 'success',
            'data' => $this->user->whereHas('sales', function ($q) use ($dealerId){
                $q->where('dealer_id', $dealerId);
                $status = ($this->request->has('status')) ? $this->request->status:'active'; 
                if ($status == 'active') $q->where('is_active', 1)->where('deleted_at', null);
                if ($status == 'pending') $q->where('is_active', 0)->where('deleted_at', null);
                if ($status == 'inactive') $q->where('deleted_at', '!=', null);
            })->latest()->paginate(10)
        ];
        
    }

    public function create($user) {

        $this->validate($this->request, [
            'company_type' => 'required',
            'name' => 'required',
            'trademark' => 'required',
            'address' => 'required',
            'city_id' => 'required',
        ]);

        $dealer = $this->dealer->create([
            'company_type' => $this->request->company_type,
            'name' => $this->request->name,
            'trademark' => $this->request->trademark,
            'address' => $this->request->address,
            'city_id' => $this->request->city_id,
            'user_id' => $user->id,
        ]);

        return $this->dealer;
    }

    public function update($id) {
        $dealer = $this->dealer->findOrFail($id);
        try {
            $dealer->update($this->request->only(['fullname', 'email', 'nik', 'noaccount', 'phone', 'photo', 'warning', 'token', 'role']));
        } catch (Exception $e) {
            return [
                'status' => 'failed',
                'message' => "Something error while updating vehicle, please try again."
            ];
        }

        return [
            'status' => 'success',
            'data' => ''
        ];
    }

    public function destroy($id) {
        $dealer = $this->dealer->findOrFail($id);

        try {
            $dealer->delete();
        } catch (Exception $e) {
            return [
                'status' => 'failed',
                'message' => "Something error while deleting vehicle, please try again."
            ];
        }

        return [
            'status' => 'success',
            'data' => ''
        ];
    }

    public function salesPending() {
        $dealer = $this->dealer
        ->select('id', 'name')
        ->where('user_id', $this->request->auth->id)
        ->has('salesPending')->with('salesPending')
        ->latest()
        ->get();

        return [
            'status' => 'success',
            'data' => $dealer
        ];
    }

    public function salesApprove() {
        $sales = $this->user->with('sales')
        ->findOrFail($this->request->sales_pending_id);

        $sales->sales()->updateExistingPivot($this->request->dealer_id, ['is_active' => 1]);

        return [
            'status' => 'success',
            'message' => 'Sales has been approved.'
        ];
    }

    public function salesReject(){
        $sales = $this->user->with('sales')
        ->findOrFail($this->request->sales_pending_id);

        $sales->sales()->updateExistingPivot($this->request->dealer_id, ['deleted_at' => date('Y-m-d h:i:s')]);

        return [
            'status' => 'success',
            'message' => 'Sales has been reject.'
        ];
    }

}
