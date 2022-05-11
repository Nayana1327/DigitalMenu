<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Models\Menu;
use App\Models\Table;
use App\Models\Orders;
use App\Models\Waiter;
use App\Models\Category;
use App\Models\DeviceToken;
use Illuminate\Support\Str;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Api\BaseController as BaseController;

class ApiController extends BaseController
{
    public function __construct()
    {
        $this->success  = true;
        $this->code['http_ok']              = Response::HTTP_OK;
        $this->code['http_created']         = Response::HTTP_CREATED;
        $this->code['http_unauthorized']    = Response::HTTP_UNAUTHORIZED;
        $this->code['http_not_found']       = Response::HTTP_NOT_FOUND;
        $this->data     = NULL;
        $this->message  = '';
    }

    //Category Listing Api
    public function listCategories(){
       $categories = Category::select('id as categoryId','category_name as categoryName', 'category_image as categoryImage')
                     ->get()->toArray();

        foreach($categories as $key => $value){
            $categories[$key]['categoryImage'] = asset('storage/category_images/' . $value['categoryImage']);
        }

        if($categories){
            $this->data = $categories;
            //return success response
            return response()->json([
                'success'   => $this->success,
                'message'   => 'Categories fetched successfully',
                'data'      => $this->data
            ], $this->code['http_created']);
        }
        $this->success  = false;
        return response()->json([
            'success'   => $this->success,
            'message'   => 'No Category Found',
            'errorData' => $this->data
        ], $this->code['http_not_found']);
    }

    //Menu Listing Api
    public function listMenus(){
        $menus = Menu::where('menu_status', 1)
                 ->select('id as menuId','menu_name as menuName','menu_description as menuDescription','menu_category as menuCategory','menu_cuisine as menuCuisine','menu_portion as menuPortion','menu_price as menuPrice','menu_image as menuImage','sub_category as subCategory')
                 ->get()->toArray();

        if($menus){
            $this->data = $menus;
            //return success response
            return response()->json([
                'success'   => $this->success,
                'message'   => 'Menus fetched successfully',
                'data'      => $this->data
            ], $this->code['http_created']);
        }
        $this->success  = false;
        return response()->json([
            'success'   => $this->success,
            'message'   => 'No Menu Found',
            'errorData'      => $this->data
        ], $this->code['http_not_found']);
    }

    //Available Table Listing Api
    public function listTables(){
        $tables = Table::select('id as tableId','table_no as tableNo', 'status')->get()->toArray();

        if($tables){
            foreach($tables as $key => $value){
                $tables[$key]['status'] = (($value['status'] == 1) ? true:false);
            }
            $this->data = $tables;
            //return success response
            return response()->json([
                'success'   => $this->success,
                'message'   => 'Tables fetched successfully',
                'data'      => $this->data
            ], $this->code['http_created']);
        }
        $this->success  = false;
        return response()->json([
            'success'   => $this->success,
            'message'   => 'No Table Available',
            'errorData'      => $this->data
        ], $this->code['http_not_found']);
    }


    //Table Selection Api
    public function selectTable(Request $request){
        $data       = $request->only('tableId');
        // $validator  = Validator::make($data, [
        //     'tableId'      => 'required|exists:tables,id'
        // ]);
        // if($validator->fails()){
        //     return $this->sendError('Validation Error.', $validator->errors());
        // }
        //check if table already reserved
        $check = Table::where('id', $request->tableId)->first();
        if($check->status == '1'){
            $is_table_update = $check->update([
                                                'status'     => 0,
                                                'updated_at' => date('Y-m-d H:i:s'),
                                            ]);
            return $this->sendResponse($is_table_update , 'Tables selected successfully. Start exploring our food.');
        }
        else{
            return $this->sendError('Table reserved already');
        }

    }

    //Menu Listing Api By (CategoryName, Veg/Non-Veg, Search)
    public function searchByMenu(Request $request){
        $categoryName = ($request->categoryName == 'All Categories') ? null : $request->categoryName;
        $subCategory = $request->subCategory;
        $searchValue = $request->searchValue;
        $menus = Menu::select('id as menuId','menu_name as menuName','menu_description as menuDescription','menu_category as menuCategory','menu_cuisine as menuCuisine','menu_portion as menuPortion','menu_price as menuPrice','menu_image as menuImage','sub_category as subCategory')
                      ->where('menu_status', 1)
                      ->where(function($query) use ($categoryName) {
                            $query->where('menu_category', 'like', '%' .$categoryName. '%')
                            ->orWhere('menu_description', 'like', '%' .$categoryName. '%');
                        })
                        ->where(function($query) use ($subCategory) {
                            $query->where('sub_category', $subCategory)
                                  ->orWhere('menu_description','like', '%'.$subCategory.'%');
                            })
                        ->where(function($query) use ($searchValue) {
                        $query->where('menu_name', 'like', '%'.$searchValue.'%')
                              ->orWhere('menu_description','like', '%'.$searchValue.'%');
                        })
                       ->get()->toArray();

        foreach($menus as $key => $value){
            $menus[$key]['menuImage'] = asset('storage/menu_item_images/' . $value['menuImage']);
        }

    if($menus){
        $this->data = $menus;
        //return success response
        return response()->json([
        'success'   => $this->success,
        'message'   => 'Menus fetched successfully',
        'data'      => $this->data
        ], $this->code['http_created']);
    }
        $this->success  = false;
        return response()->json([
        'success'   => $this->success,
        'message'   => 'No search found',
        'errorData'      => $this->data
        ], $this->code['http_ok']);
    }

    public function insertOrder(Request $request){
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
                'message'   => 'Order cant be placed with this table.',
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

        // $this->data = [
        //         'OrderId'   => $order_insert->id
        //     ];

        //order created, return success response
        return response()->json([
            'success'   => $this->success,
            'message'   => 'Order has been placed',
            'data'      => $this->data
        ], $this->code['http_ok']);
    }

    public function getOrder(Request $request){
        $data   = $request->only('orderId');

        // $validator  = Validator::make($data, [
        //     'orderId'   => 'required|exists:orders,id'
        // ]);

        // //Send failed response if request is not valid
        // if ($validator->fails()) {
        //     $this->success  = false;
        //     return response()->json([
        //         'success'   => $this->success,
        //         'message'   => $validator->messages(),
        //         'data'      => $this->data
        //     ], $this->code);
        // }

        $order = Orders::where('orders.id', $request->orderId)
                    ->join('tables', 'tables.id', '=', 'orders.table_id')
                    ->leftjoin('waiters', 'waiters.id', '=', 'orders.waiter_id')
                    ->select('orders.id AS OrderId', 'tables.id AS tableID', 'tables.table_no AS tableNo', 'tables.table_name AS tableName', 'waiters.waiter_name AS waiterName', 'orders.order_status AS orderStatus', 'orders.order_total_amount AS orderTotalAmount')
                    ->get()
                    ->toArray();

        $order_details = Orders::where('orders.id', $request->orderId)
                            ->join('order_details', 'order_details.order_id', '=', 'orders.id')
                            ->join('menus', 'menus.id', '=', 'order_details.menu_id')
                            ->select('menus.id AS menuId', 'menus.menu_name AS menuName', 'menus.menu_price AS menuPrice', 'menus.sub_category AS subCategory', 'order_details.quantity AS quantity', 'order_details.menu_total_amount AS menuTotalAmount')
                            ->get()
                            ->toArray();

        if($order){
            $this->data['order'] = $order[0];
            $this->data['order']['orderDetails'] = $order_details;

            //order fetched, return success response
            return response()->json([
                'success'   => $this->success,
                'message'   => 'Order fetched successfully',
                'data'      => $this->data
                ], $this->code);
        }

        $this->success  = false;
        return response()->json([
            'success'   => $this->success,
            'message'   => "No orders found",
            'data'      => $this->data
        ], $this->code);
    }

    public function updateOrder(Request $request){
        $data       = json_decode($request->getContent(),true);

        // $validator  = Validator::make($data, [
        //     'orderId'   => 'required|exists:orders,id,deleted_at,NULL',
        //     'menuData'  => 'required|array'
        // ]);

        // //Send failed response if request is not valid
        // if ($validator->fails()) {
        //     $this->success  = false;
        //     return response()->json([
        //         'success'   => $this->success,
        //         'message'   => $validator->messages(),
        //         'data'      => $this->data
        //     ], $this->code);
        // }

        $index = 0;
        $flag = 0;

        foreach($data['menuData'] as $key => $value){
            $menu = Menu::find($value['menuId']);

            if(is_null($menu)){
                $response[$index]['message']  = 'The selected menuId is invalid.';
                $response[$index]['arrayKey'] = $key;
                $response[$index]['arrayValue']   = 'menuId';
                $index += 1;
                $flag = 1;
            }

            if(empty($value['quantity'])){
                $response[$index]['message']  = 'The selected quantity is invalid.';
                $response[$index]['arrayKey'] = $key;
                $response[$index]['arrayValue']   = 'quantity';
                $index += 1;
                $flag = 1;
            }
        }

        if($flag == 1){
            $this->success  = false;
            return response()->json([
                'success'   => $this->success,
                'message'   => 'Validation Error',
                'errorData' => $response
            ], $this->code);
        }

        foreach($data['menuData'] as $key => $value){
            $menu_price = 0;
            $order_details = [];

            $menu_price = Menu::find($value['menuId']);

            $existingMenu = OrderDetails::where([
                                                    'order_id' => $data['orderId'],
                                                    'menu_id' => $value['menuId']
                                                ])
                                            ->first();

            if($existingMenu){
                OrderDetails::where('id', $existingMenu->id)
                                ->update([
                                            'quantity' => $value['quantity'],
                                            'menu_total_amount' => $menu_price->menu_price*$value['quantity']
                                        ]);
            }else{
                $order_details = [
                    'order_id'  => $data['orderId'],
                    'menu_id'   => $value['menuId'],
                    'quantity'  => $value['quantity'],
                    'menu_total_amount' => $menu_price->menu_price*$value['quantity']
                ];

                OrderDetails::create($order_details);
            }
        }

        $order_total_amount = OrderDetails::where('order_id', $data['orderId'])->sum('menu_total_amount');

        Orders::where('id', $data['orderId'])->update(['order_total_amount' => $order_total_amount]);

        //order updated, return success response
        return response()->json([
            'success'   => $this->success,
            'message'   => 'Order updated successfully',
            'data'      => $this->data
        ], $this->code);
    }

    public function deleteOrder(Request $request){
        $data   = $request->only('orderId');

        // $validator  = Validator::make($data, [
        //     'orderId'   => 'required|exists:orders,id'
        // ]);

        // //Send failed response if request is not valid
        // if ($validator->fails()) {
        //     $this->success  = false;
        //     return response()->json([
        //         'success'   => $this->success,
        //         'message'   => $validator->messages(),
        //         'data'      => $this->data
        //     ], $this->code);
        // }

        $deleteOrder = Orders::find($request->orderId);
        if($deleteOrder){
            $table_id   = $deleteOrder->table_id;
            $deleteOrder->update(['order_status' => 'Order Cancelled']);
            $deleteOrder->delete();

            Table::where('id', $table_id)
                    ->update([
                                'status'     => 1,
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);

            //order deleted, return success response
            return response()->json([
                'success'   => $this->success,
                'message'   => 'Order cancelled successfully',
                'data'      => $this->data
            ], $this->code);
        }
        $this->success  = false;
        return response()->json([
            'success'   => $this->success,
            'message'   => "No order found",
            'data'      => $this->data
        ], $this->code);
    }

    public function getTableOrder(Request $request){
        $data   = $request->only('tableId');

        $validator  = Validator::make($data, [
            'tableId'   => 'required|exists:orders,table_id'
        ]);

        //Send failed response if request is not valid
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

            //order fetched, return success response
            return response()->json([
                'success'       => $this->success,
                'message'       => 'Order fetched successfully',
                'data'          => $this->data
                ], $this->code['http_created']);
        }

        $this->success  = false;
        return response()->json([
            'success'   => $this->success,
            'message'   => "No orders found",
            'errorData' => $this->data
        ], $this->code['http_not_found']);
    }

    public function waiterLogin(Request $request){
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
            'data'      => $this->data
            ], $this->code['http_created']);
    }

    public function orderCompletion(Request $request){
        $data   = $request->only('tableId');

        $validator  = Validator::make($data, [
            'tableId'   => 'required|exists:orders,table_id,deleted_at,NULL'
            // 'orderId'   => 'required|exists:orders,id,deleted_at,NULL'
        ]);

        //Send failed response if request is not valid
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
                'errorData'      => $this->data
            ], $this->code['http_not_found']);
        }elseif($order->order_status == "Order Cancelled"){
            $this->success  = false;
            return response()->json([
                'success'   => $this->success,
                'message'   => "This order has been cancelled already",
                'errorData'      => $this->data
            ], $this->code['http_not_found']);
        }else{
            Table::where('id', $order->table_id )->update(['status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
            $order->waiter_id = $waiter->id;
            $order->order_status = "Payment Done";
            $order->deleted_at = date('Y-m-d H:i:s');
            $order->save();
        }

        $tableNo        = Table::where('id', $data['tableId'])->first();
        $deviceToken    = DeviceToken::pluck('device_token');

        $title  = "Order Completed";
        $body   = "The order in ". $tableNo->table_no ."has been completed";

        $this->sendNotification($title, $body, $deviceToken);

        // $orderCompletion = Orders::where('orders.table_id', $data['tableId'])
        //                             ->join('tables', 'tables.id', '=', 'orders.table_id')
        //                             ->leftjoin('waiters', 'waiters.id', '=', 'orders.waiter_id')
        //                             ->select('orders.id AS OrderId', 'tables.id AS tableID', 'tables.table_no AS tableNo', 'tables.table_name AS tableName', 'waiters.waiter_name AS waiterName', 'orders.order_status AS orderStatus', 'orders.order_total_amount AS orderTotalAmount')
        //                             ->withTrashed()
        //                             ->get()
        //                             ->toArray();

        // $order_details = Orders::where('orders.table_id', $data['tableId'])
        //                             ->join('order_details', 'order_details.order_id', '=', 'orders.id')
        //                             ->join('menus', 'menus.id', '=', 'order_details.menu_id')
        //                             ->select('menus.id AS menuId', 'menus.menu_name AS menuName', 'menus.menu_price AS menuPrice', 'menus.sub_category AS subCategory', 'order_details.quantity AS quantity', 'order_details.menu_total_amount AS menuTotalAmount')
        //                             ->withTrashed()
        //                             ->get()
        //                             ->toArray();

        // $this->data['order'] = $orderCompletion[0];
        // $this->data['order']['orderDetails'] = $order_details;

        return response()->json([
            'success'   => $this->success,
            'message'   => 'Order has been completed.',
            'data'      => $this->data
            ], $this->code['http_ok']);
    }

    public function sendNotification($title, $body, $deviceToken){
        $SERVER_API_KEY = env('FCM_SERVER_KEY');

        $data = [
            "registration_ids" => $deviceToken,
            "notification" => [
                "title" => $title,
                "body" => $body
            ]
        ];

        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        $response = curl_exec($ch);

        return $response;
    }

    public function deleteMenuItems(Request $request){
        $data   = $request->only('tableId', 'menuId');

        $validator  = Validator::make($data, [
            'tableId'   => 'required|exists:orders,table_id,deleted_at,NULL',
            'menuId'   => 'required|exists:menus,id'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $this->success  = false;
            return response()->json([
                'success'   => $this->success,
                'message'   => "Some error occured while deleting menu items from the order.",
                'errorData'      => $validator->messages()
            ], $this->code['http_not_found']);
        }

        $order = Orders::where('table_id', $data['tableId'])->first();
        $orderDetails = OrderDetails::where(['order_id' => $order->id, 'menu_id' => $data['menuId']])->first();

        if(empty($orderDetails)){
            $this->success  = false;
            return response()->json([
                'success'   => $this->success,
                'message'   => "The selected menu is not ordered in the table",
                'errorData'      => $this->data
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
            'success'   => $this->success,
            'message'   => "Menu item deleted.",
            'data'      => $this->data
        ], $this->code['http_ok']);
    }

    public function deviceToken(Request $request){

        $data   = $request->only('deviceId', 'deviceToken');

        $validator  = Validator::make($data, [
            'deviceId'  => 'required',
            'deviceToken'   => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $this->success  = false;
            return response()->json([
                'success'   => $this->success,
                'message'   => "Some error occured while adding new device.",
                'errorData'   => $validator->messages()
            ], $this->code['http_not_found']);
        }

        $presentDevice = DeviceToken::where('device_id', $data['deviceId'])->first();

        if($presentDevice){
            $presentDevice->device_token = $data['deviceToken'];
            $presentDevice->save();

            return response()->json([
                'success'   => $this->success,
                'message'   => "Device token has been updated",
                'data'      => $this->data
            ], $this->code['http_ok']);
        }

        $value = [
            'device_id' => $data['deviceId'],
            'device_token'  => $data['deviceToken']
        ];

        DeviceToken::create($value);

        return response()->json([
            'success'   => $this->success,
            'message'   => "Device token has been added",
            'data'      => $this->data
        ], $this->code['http_ok']);
    }

    public function test(){
        echo "success";
    }
}

