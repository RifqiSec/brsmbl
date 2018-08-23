<?php

namespace App\Http\Controllers;

use Validator;
use App\Vehicle;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
class VehicleController extends Controller 
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
    public function __construct(Request $request, Vehicle $vehicle) {
        $this->request = $request;
        $this->vehicle = $vehicle;
    }

    public function index() {

        $this->vehicle = $this->vehicle->with('type', 'brand');
        if($this->request->has('type')) {
            // $this->vehicle = $this->vehicle->type()->where('name', $this->request->type);
        }
        return [
            'status' => 'success',
            'data' => $this->vehicle->latest()->paginate(10)
        ];
    }

    public function show($id) {
        return [
            'status' => 'success',
            'data' => $this->vehicle->findOrFail($id)
        ];
    }

    public function update($id) {
        $vehicle = $this->vehicle->findOrFail($id);
        try {
            $vehicle->update($this->request->only(['fullname', 'email', 'nik', 'noaccount', 'phone', 'photo', 'warning', 'token', 'role']));
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
        $vehicle = $this->vehicle->findOrFail($id);

        try {
            $vehicle->delete();
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

}
