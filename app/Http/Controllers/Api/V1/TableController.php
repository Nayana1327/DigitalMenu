<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController as BaseController;

use App\Models\Table;

class TableController extends BaseController
{
    /**
     * Listing all the shop tables from the databse.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $tables = Table::select('id as tableId','table_no as tableNo', 'status')
                        ->get()
                        ->toArray();

        if($tables){
            foreach($tables as $key => $value){
                $tables[$key]['status'] = (($value['status'] == 1) ? true:false);
            }

            $this->data = $tables;
            return response()->json([
                'success'       => $this->success,
                'message'       => 'Tables fetched successfully',
                'successData'   => $this->data
            ], $this->code['http_created']);
        }

        $this->success  = false;
        return response()->json([
            'success'   => $this->success,
            'message'   => 'No Table Available',
            'errorData'      => $this->data
        ], $this->code['http_not_found']);
    }
}
