<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Models\Menu;
use App\Models\Table;
use App\Models\Orders;
use App\Models\Category;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Api\BaseController as BaseController;

class ApiController extends BaseController
{
    public function __construct()
    {
        // $this->user     = JWTAuth::parseToken()->authenticate();
        $this->data     = [];
        $this->code     = Response::HTTP_OK;
        $this->success  = true;
        $this->message  = '';
    }

    //Category Listing Api
    public function listCategories(){
       $categories = Category::select('id as categoryId','category_name as categoryName')
                     ->get()->toArray();

        if($categories){
            $this->data = $categories;
            //return success response
            return response()->json([
                'success'   => $this->success,
                'message'   => 'Categories fetched successfully',
                'data'      => $this->data
            ], $this->code);
        }
        $this->success  = false;
        return response()->json([
            'success'   => $this->success,
            'message'   => 'No Category Found',
            'data'      => $this->data
        ], $this->code);
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
            ], $this->code);
        }
        $this->success  = false;
        return response()->json([
            'success'   => $this->success,
            'message'   => 'No Menu Found',
            'data'      => $this->data
        ], $this->code);
    }

    //Available Table Listing Api
    public function listTables(){
        $tables = Table::where('status', 1)
                        ->select('id as tableId','table_no as tableNo')
                        ->get()->toArray();

        if($tables){
            $this->data = $tables;
            //return success response
            return response()->json([
                'success'   => $this->success,
                'message'   => 'Tables fetched successfully',
                'data'      => $this->data
            ], $this->code);
        }
        $this->success  = false;
        return response()->json([
            'success'   => $this->success,
            'message'   => 'No Table Available',
            'data'      => $this->data
        ], $this->code);
    }


    //Table Selection Api
    public function selectTable(Request $request){
        $data       = $request->only('tableId');
        $validator  = Validator::make($data, [
            'tableId'      => 'required|exists:tables,id'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
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
        $categoryName = $request->categoryName;
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

    if($menus){
        $this->data = $menus;
        //return success response
        return response()->json([
        'success'   => $this->success,
        'message'   => 'Menus fetched successfully',
        'data'      => $this->data
        ], $this->code);
    }
        $this->success  = false;
        return response()->json([
        'success'   => $this->success,
        'message'   => 'No search found',
        'data'      => $this->data
        ], $this->code);
    }

    public function insertOrder(Request $request){
        //Validate data
        $data       = json_decode($request->getContent(),true);
        $validator  = Validator::make($data, [
            'tableId'   => 'required|exists:tables,id,status,0',
            'menuData'  => 'required|array'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $this->success  = false;
            return response()->json([
                'success'   => $this->success,
                'message'   => $validator->messages(),
                'data'      => $this->data
            ], $this->code);
        }

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

        $existingOrder = Orders::where('table_id', $data['tableId'])->get('id')->toArray();

        if($existingOrder){
            $this->success  = false;
            return response()->json([
                'success'   => $this->success,
                'message'   => "Table order already exist. Please update.",
                'data'      => $this->data
            ], $this->code);
        }

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

        $this->data = [
                'OrderId'   => $order_insert->id
            ];

        //order created, return success response
        return response()->json([
            'success'   => $this->success,
            'message'   => 'Order created successfully',
            'data'      => $this->data
        ], $this->code);
    }

    public function getOrder(Request $request){
        $data   = $request->only('orderId');

        $validator  = Validator::make($data, [
            'orderId'   => 'required|exists:orders,id'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $this->success  = false;
            return response()->json([
                'success'   => $this->success,
                'message'   => $validator->messages(),
                'data'      => $this->data
            ], $this->code);
        }

        $order = Orders::where('orders.id', $request->orderId)
                    ->join('tables', 'tables.id', '=', 'orders.table_id')
                    ->leftjoin('waiters', 'waiters.id', '=', 'orders.waiter_id')
                    ->select('orders.id AS OrderId', 'tables.id AS tableID', 'tables.table_no AS tableNo', 'tables.table_name AS tableName', 'waiters.waiter_name AS waiterName', 'orders.order_status AS orderStatus', 'orders.order_total_amount AS orderTotalAmount')
                    ->get()
                    ->toArray();

        $order_details = Orders::where('orders.id', $request->orderId)
                            ->join('order_details', 'order_details.order_id', '=', 'orders.id')
                            ->join('menus', 'menus.id', '=', 'order_details.menu_id')
                            ->select('menus.id AS menuId', 'menus.menu_name AS menuName', 'menus.menu_price AS menuPrice', 'order_details.quantity AS quantity', 'order_details.menu_total_amount AS menuTotalAmount')
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

        $validator  = Validator::make($data, [
            'orderId'   => 'required|exists:orders,id,deleted_at,NULL',
            'menuData'  => 'required|array'
            // 'menuData.*.menuId'     => 'required|exists:menus,id',
            // 'menuData.*.quantity'   => 'required',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $this->success  = false;
            return response()->json([
                'success'   => $this->success,
                'message'   => $validator->messages(),
                'data'      => $this->data
            ], $this->code);
        }

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

        $validator  = Validator::make($data, [
            'orderId'   => 'required|exists:orders,id'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $this->success  = false;
            return response()->json([
                'success'   => $this->success,
                'message'   => $validator->messages(),
                'data'      => $this->data
            ], $this->code);
        }

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
    public function tableOrder(Request $request){

    }
}
