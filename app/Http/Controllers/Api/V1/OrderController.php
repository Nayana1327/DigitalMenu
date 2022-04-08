<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController as BaseController;
use Illuminate\Http\Request;

use Validator;

use App\Models\Menu;
use App\Models\Table;
use App\Models\Orders;
use App\Models\Waiter;
use App\Models\DeviceToken;
use App\Models\OrderDetails;

class OrderController extends BaseController
{
    /**
     * Store a newly created order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $data   = json_decode($request->getContent(),true);
        $index  = 0;
        $flag   = 0;

        if(empty($data['tableId'])){
            $response[$index]['message']  = 'Please enter table id.';
            $response[$index]['fieldIndex'] = 0;
            $response[$index]['fieldValue'] = 'tableId';
            $index += 1;
            $flag = 1;
        }else{
            $tableValidation = Table::where('id', $data['tableId'])->first();
            $tableOrderValidation = Orders::where('table_id', $data['tableId'])->first();

            if(empty($tableValidation)){
                $response[$index]['message']  = 'Please enter valid table id.';
                $response[$index]['fieldIndex'] = 0;
                $response[$index]['fieldValue'] = 'tableId';
                $index += 1;
                $flag = 1;
            }else{
                if($tableOrderValidation){
                    $response[$index]['message']  = 'Selected table already have an order.';
                    $response[$index]['fieldIndex'] = 0;
                    $response[$index]['fieldValue'] = 'tableId';
                    $index += 1;
                    $flag = 1;
                }
            }
        }

        if(empty($data['menuData'])){
            $response[$index]['message']  = 'Please enter menuData.';
            $response[$index]['fieldIndex'] = 0;
            $response[$index]['fieldValue'] = 'menuData';
            $index += 1;
            $flag = 1;
        }else{
            foreach($data['menuData'] as $key => $value){
                $menu = Menu::find($value['menuId']);

                if(is_null($menu)){
                    $response[$index]['message']  = 'The selected menuId is invalid.';
                    $response[$index]['fieldIndex'] = $key;
                    $response[$index]['fieldValue'] = 'menuId';
                    $index += 1;
                    $flag = 1;
                }

                if(empty($value['quantity'])){
                    $response[$index]['message']  = 'The selected quantity is invalid.';
                    $response[$index]['fieldIndex'] = $key;
                    $response[$index]['fieldValue'] = 'quantity';
                    $index += 1;
                    $flag = 1;
                }
            }
        }

        if($flag == 1){
            $this->success  = false;
            return response()->json([
                'success'   => $this->success,
                'message'   => 'Some error occured with the given order details.',
                'errorData' => $response
            ], 
            $this->code['http_not_found']);
        }

        Table::where('id', $data['tableId'])
                ->update([
                            'status'     => 0,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

        $order = [
            'table_id'  => $data['tableId']
        ];

        $order_insert = Orders::create($order);

        $order_total_amount = 0;

        foreach($data['menuData'] as $key => $value){
            $menu_price = 0;
            $order_details = [];

            $menu_price = Menu::find($value['menuId']);

            $order_details = [
                'order_id'  => $order_insert->id,
                'menu_id'   => $value['menuId'],
                'quantity'  => $value['quantity'],
                'menu_total_amount' => $menu_price->menu_price*$value['quantity']
            ];

            OrderDetails::create($order_details);

            $order_total_amount += $menu_price->menu_price*$value['quantity'];
        }

        Orders::where('id', $order_insert->id)->update(['order_total_amount' => $order_total_amount]);

        return response()->json([
            'success'   => $this->success,
            'message'   => 'Order has been placed',
            'successData'      => $this->data
        ], $this->code['http_ok']);
    }
    
    /**
     * Get orders in the table
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getOrder(Request $request)
    {
        $data   = $request->only('tableId');

        $validator  = Validator::make($data, [
            'tableId'   => 'required|exists:orders,table_id'
        ]);

        if ($validator->fails()) {
            $this->success  = false;
            return response()->json([
                'success'   => $this->success,
                'message'   => "No order has been placed in the table",
                'errorData' => $this->data
            ], $this->code['http_not_found']);
        }

        $order = Orders::where('orders.table_id', $request->tableId)
                        ->join('tables', 'tables.id', '=', 'orders.table_id')
                        ->leftjoin('waiters', 'waiters.id', '=', 'orders.waiter_id')
                        ->select('orders.id AS OrderId', 'tables.id AS tableID', 'tables.table_no AS tableNo', 'tables.table_name AS tableName', 'waiters.waiter_name AS waiterName', 'orders.order_status AS orderStatus', 'orders.order_total_amount AS orderTotalAmount')
                        ->get()
                        ->toArray();

        $order_details = Orders::where('orders.table_id', $request->tableId)
                                ->join('order_details', 'order_details.order_id', '=', 'orders.id')
                                ->join('menus', 'menus.id', '=', 'order_details.menu_id')
                                ->select('menus.id AS menuId', 'menus.menu_name AS menuName', 'menus.menu_price AS menuPrice', 'menus.sub_category AS subCategory', 'order_details.quantity AS quantity', 'order_details.menu_total_amount AS menuTotalAmount')
                                ->get()
                                ->toArray();

        if($order_details){
            $this->data['order'] = $order[0];
            $this->data['order']['orderDetails'] = $order_details;

            return response()->json([
                'success'       => $this->success,
                'message'       => 'Order fetched successfully',
                'successData'   => $this->data
                ], $this->code['http_created']);
        }

        $this->success  = false;
        return response()->json([
            'success'   => $this->success,
            'message'   => "No orders found",
            'errorData' => $this->data
        ], $this->code['http_not_found']);
    }
   
    /**
     * Delete menu items from the order
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteMenuOrder(Request $request)
    {
        $data   = $request->only('tableId', 'menuId');

        $validator  = Validator::make($data, [
            'tableId'   => 'required|exists:orders,table_id,deleted_at,NULL',
            'menuId'   => 'required|exists:menus,id'
        ]);

        if ($validator->fails()) {
            $this->success  = false;
            return response()->json([
                'success'   => $this->success,
                'message'   => "Some error occured while deleting menu items from the order.",
                'errorData' => $validator->messages()
            ], $this->code['http_not_found']);
        }

        $order = Orders::where('table_id', $data['tableId'])->first();
        $orderDetails = OrderDetails::where(['order_id' => $order->id, 'menu_id' => $data['menuId']])->first();

        if(empty($orderDetails)){
            $this->success  = false;
            return response()->json([
                'success'   => $this->success,
                'message'   => "The selected menu is not ordered in the table",
                'errorData' => $this->data
            ], $this->code['http_not_found']);
        }

        $orderDetails->delete();

        $totalAmount = 0;

        $orderDetailsTotal = OrderDetails::where('order_id', $order->id)->get()->toArray();

        if($orderDetailsTotal){
            foreach($orderDetailsTotal as $value){
                $totalAmount += $value['menu_total_amount'];
            }

            Orders::where('id', $order->id)->update(['order_total_amount' => $totalAmount]);
        } else {
            Orders::where('id', $order->id)
                    ->update([
                                'order_total_amount' => "0.00",
                                'order_status'  => 'Order Cancelled',
                                'deleted_at'    => date('Y-m-d H:i:s')
                            ]);
        }

        return response()->json([
            'success'       => $this->success,
            'message'       => "Menu item deleted.",
            'successData'   => $this->data
        ], $this->code['http_ok']);
    }
   
    /**
     * Update status for order completion from waiter side
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function orderCompletion(Request $request)
    {
        $data   = $request->only('tableId');

        $validator  = Validator::make($data, [
            'tableId'   => 'required|exists:orders,table_id,deleted_at,NULL'
        ]);

        if ($validator->fails()) {
            $this->success  = false;
            return response()->json([
                'success'   => $this->success,
                'message'   => "Some error occured while completeing the order.",
                'errorData' => $validator->messages()
            ], $this->code['http_not_found']);
        }

        $waiter = Waiter::where("remember_token", "=", $request->headers->get("Authorization"))->first();

        $order = Orders::where('table_id', $data['tableId'])->first();

        if($order->order_status == "Payment Done"){
            $this->success  = false;
            return response()->json([
                'success'   => $this->success,
                'message'   => "This order already completed and the payment is done",
                'errorData' => $this->data
            ], $this->code['http_not_found']);
        }elseif($order->order_status == "Order Cancelled"){
            $this->success  = false;
            return response()->json([
                'success'   => $this->success,
                'message'   => "This order has been cancelled already",
                'errorData' => $this->data
            ], $this->code['http_not_found']);
        }else{
            Table::where('id', $order->table_id)->update(['status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
            $order->waiter_id = $waiter->id;
            $order->order_status = "Payment Done";
            $order->deleted_at = date('Y-m-d H:i:s');
            $order->save();
        }

        $tableNo        = Table::where('id', $data['tableId'])->first();
        $deviceToken    = DeviceToken::select('device_token')->get()->toArray();

        $title  = "Order Completed";
        $body   = "The order in ". $tableNo->table_no ."has been completed"; 

        $this->sendNotification($title, $body, $deviceToken);

        return response()->json([
            'success'   => $this->success,
            'message'   => 'Order has been completed.',
            'successData'      => $this->data
            ], $this->code['http_ok']);
    }
}
