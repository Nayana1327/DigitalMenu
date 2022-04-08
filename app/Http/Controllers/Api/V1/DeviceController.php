<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController as BaseController;
use Illuminate\Http\Request;

use Validator;

use App\Models\DeviceToken;

class DeviceController extends BaseController
{
    /**
     * Store or update device id and device token to sent firebase notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data   = $request->only('deviceId', 'deviceToken');

        $validator  = Validator::make($data, [
            'deviceId'  => 'required',
            'deviceToken'   => 'required'
        ]);
        
        if ($validator->fails()) {
            $this->success  = false;
            return response()->json([
                'success'   => $this->success,
                'message'   => "Some error occured while adding new device.",
                'errorData' => $validator->messages()
            ], $this->code['http_not_found']);
        }

        $presentDevice = DeviceToken::where('device_id', $data['deviceId'])->first();

        if($presentDevice){
            $presentDevice->device_token = $data['deviceToken'];
            $presentDevice->save();

            return response()->json([
                'success'       => $this->success,
                'message'       => "Device token has been updated",
                'successData'   => $this->data
            ], $this->code['http_ok']);
        }

        $value = [
            'device_id'     => $data['deviceId'],
            'device_token'  => $data['deviceToken']
        ];

        DeviceToken::create($value);

        return response()->json([
            'success'       => $this->success,
            'message'       => "Device token has been added",
            'successData'   => $this->data
        ], $this->code['http_ok']);
    }

    /**
     * Device tokens table truncate using laravel elquent.
     */
    // public function truncate()
    // {
    //     DeviceToken::truncate();
    // }
}
