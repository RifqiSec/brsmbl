<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vehicle_type_id', 'vehicle_brand_id', 'name', 'harga', 'fuel', 'fuel_consumption', 'engine', 'hp', 'torque', 'transmition', 'gear_box', 'wd_type', 'cylinder', 'seat', 'door', 'dimension', 'fuel_tank', 'velg', 'front_brake', 'rear_brake', 'net_weight', 'photo'
    ];

    public function type() {
        return $this->belongsTo('App\VehicleType', 'vehicle_type_id');
    }

    public function brand() {
        return $this->belongsTo('App\VehicleBrand', 'vehicle_brand_id');
    }

    public function dealer() {
        return $this->belongsToMany('App\Dealer', 'dealer_vehicle');
    }

    public function sales(){
        return $this->belongsToMany('App\User', 'sales_vehicles', 'vehicle_id', 'sales_id');
    }

}
