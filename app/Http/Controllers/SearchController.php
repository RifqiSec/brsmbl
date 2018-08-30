<?php

namespace App\Http\Controllers;

use Validator;
use App\Vehicle;
use App\Dealer;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
class SearchController extends Controller 
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
    public function __construct(Request $request, Vehicle $vehicle, Dealer $dealer) {
        $this->request = $request;
        $this->vehicle = $vehicle;
        $this->dealer = $dealer;
    }

    public function index() {
        $req = [];
        foreach($this->request->all() as $key => $value) {
            $req[str_replace("\\", '', $key)] = $value;
        }
        if(isset($req['brand'])) {
            $this->vehicle = $this->vehicle->whereHas('brand', function ($query) use ($req) {
                $query->where('id', $req['brand']);
            });
        }

        if(isset($req['model'])) {
            $this->vehicle = $this->vehicle->where('id', $req['model']);
        }

        if(isset($req['type'])) {
            // $this->vehicle = $this->vehicle->where('fuel', $req['type']);
        }

        if(isset($req['min'])) {
            $this->vehicle = $this->vehicle->where('harga', '>=', $req['min']);
        }

        if(isset($req['max'])) {
            $this->vehicle = $this->vehicle->where('harga', '<=', $req['max']);
        }

        if(isset($req['area'])) {
            $this->vehicle = $this->vehicle->whereHas('sales.sales.city', function ($query) use ($req){
                $query->where('city_id', $req['area']);
            });
        }
        $this->vehicle->update(['search' => \DB::raw('search+1')]);
        return [
            'status' => 'success',
            'data' => $this->vehicle
            ->select('id', 'name', 'harga', 'engine', 'gear_box', 'photo', 'vehicle_brand_id', 'vehicle_type_id')
            ->with('brand', 'sales')
            ->withCount('sales')
            ->latest()
            ->paginate(10),
        ];
    }
}
 	