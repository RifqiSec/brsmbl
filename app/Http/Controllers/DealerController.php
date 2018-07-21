<?php

namespace App\Http\Controllers;

use Validator;
use App\Dealer;
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
    public function __construct(Request $request, Dealer $dealer) {
        $this->request = $request;
        $this->dealer = $dealer;
    }

    public function index() {
        return [
            'status' => 'success',
            'data' => $this->dealer->paginate(10)
        ];
    }

    public function show($id) {
        return [
            'status' => 'success',
            'data' => $this->dealer->findOrFail($id)
        ];
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

}
