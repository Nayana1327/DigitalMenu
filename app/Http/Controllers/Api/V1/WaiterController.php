<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController as BaseController;
use Illuminate\Http\Request;

use App\Models\Waiter;
use Illuminate\Support\Str;

class WaiterController extends BaseController
{
    /**
     * Waiter login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $data       = json_decode($request->getContent(),true);

        if((empty($data['email'])) || (empty($data['password']))){
            $this->success  = false;
            return response()->json([
                'success'   => $this->success,
                'message'   => "Please enter valid mail id and password.",
                'errorData' => $this->data
            ], $this->code['http_not_found']);
        }

        $waiter = Waiter::where('email', $data['email'])->first();

        if(empty($waiter)){
            $this->success  = false;
            return response()->json([
                'success'   => $this->success,
                'message'   => "This user is not rgeistered.",
                'errorData' => $this->data
            ], $this->code['http_unauthorized']);
        }

        if($waiter->password != $data['password']){
            $this->success  = false;
            return response()->json([
                'success'   => $this->success,
                'message'   => "The entered password is incorrect",
                'errorData' => $this->data
            ], $this->code['http_unauthorized']);
        }

        do {
            $token = Str::random(50);
        } while (Waiter::where('remember_token', '=', $token)->first() instanceof Waiter);

        Waiter::where('id', $waiter->id)
                ->update([
                            'remember_token'    => $token,
                            'updated_at'    => date('Y-m-d H:i:s')
                        ]);

        $this->data['token'] = $token;

        return response()->json([
            'success'   => $this->success,
            'message'   => 'Logged In successfully.',
            'successData'      => $this->data
            ], $this->code['http_created']);
    }
}
