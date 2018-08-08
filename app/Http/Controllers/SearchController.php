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
        $this->vehicle = $this->vehicle->has('dealer');
        
        if($this->request->get('brand') != '') {
            $this->vehicle = $this->vehicle->whereHas('brand', function ($query) {
                $query->where('name', $this->request->get('brand'));
            });
        }

        if($this->request->get('model') != '') {
            if ($this->request->get('model') == 'automatic') {
                $model = 'A/T';
            }elseif ($this->request->get('model') == 'manual') {
                $model = 'M/T';
            }else{
                $model = 'hybird';
            }
            $this->vehicle = $this->vehicle->where('gear_box', $model);
        }

        if($this->request->get('type') != '') {
            $this->vehicle = $this->vehicle->where('fuel', $this->request->get('type'));
        }

        if($this->request->get('min') != '') {
            $this->vehicle = $this->vehicle->where('harga', '>=', $this->request->get('min'));
        }

        if($this->request->get('max') != '') {
            $this->vehicle = $this->vehicle->where('harga', '<=', $this->request->get('max'));
        }

        if($this->request->get('area') != '') {
            $this->vehicle = $this->vehicle->whereHas('dealer.city', function ($query) {
                $query->where('city_id', $this->request->get('area'));
            });
        }

        return [
            'status' => 'success',
            'data' => $this->vehicle->select('name', 'harga', 'engine', 'gear_box', 'photo', 'vehicle_brand_id', 'vehicle_type_id')->with('type','brand', 'dealer')->withCount('dealer')->paginate(10),
        ];
    }
}
 	