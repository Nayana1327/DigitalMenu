<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Table;
use App\Models\UserOrder;
use App\Models\ItemOrdered;
use Validator;
use App\Http\Resources\Category as CategoryResource;
use App\Http\Resources\Menu as MenuResource;
use App\Http\Resources\Table as TableResource;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends BaseController
{
    public function __construct()
    {
        // $this->user     = JWTAuth::parseToken()->authenticate();
        // $this->data     = [];
        $this->code     = Response::HTTP_OK;
        $this->success  = true;
        $this->message  = '';
    }

    //Category Listing Api
    public function listCategories(){
       $categories = Category::select('id as categoryId','category_name as categoryName')
                     ->get();

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
            'message'   => 'Something went wrong',
            'data'      => $this->data
        ], $this->code);
    }

    //Menu Listing Api
    public function listMenus(){
        $menus = Menu::where('menu_status', 1)
                 ->select('id as menuId','menu_name as menuName','menu_description as menuDescription','menu_category as menuCategory','menu_cuisine as menuCuisine','menu_portion as menuPortion','menu_price as menuPrice','menu_image as menuImage','sub_category as subCategory')
                 ->get();

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
            'message'   => 'Something went wrong',
            'data'      => $this->data
        ], $this->code);
    }

    //Available Table Listing Api
    public function listTables(){
        $tables = Table::where('status', 1)
                  ->select('id as tableId','table_no as tableNo')
                  ->get();

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
            'message'   => 'Something went wrong',
            'data'      => $this->data
        ], $this->code);
    }


    //Table Selection Api
    public function selectTable(Request $request){
        $data       = $request->only('tableId');
        $validator  = Validator::make($data, [
            'tableId'      => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        //check if table already reserved
        $check = Table::where('id', $request->tableId)->first();
        if($check->status == '1'){
            if($check)
            {
                $is_table_update = $check->update([
                        'status'     => 0,
                        'updated_at' => date('Y-m-d H:i:s'),
                     ]);

                if($is_table_update){
                    return $this->sendResponse($is_table_update , 'Tables selected successfully. Start exploring our food.');
                }
                else{
                    return $this->sendError('No data found');
                }
            }
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
                       ->get();

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
        'message'   => 'Something went wrong',
        // 'data'      => $this->data
        ], $this->code);
    }

    public function insertOrders(Request $request)
    {
        //Validate data
        $data       = $request->only('tableId','menuId','quantity');
        $validator  = Validator::make($data, [
            'tableId'   => 'required|exists:tables,id',
            'menuId'    => 'required|exists:menus,id',
            'quantity'  => 'required',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $this->success  = false;
            return response()->json([
                'success'   => $this->success,
                'message'   => $validator->messages(),
                // 'data'      => $this->data
            ], $this->code);
        }
        $tableNo = Table::where('id', $request->tableId)->first();

        //Request is valid, create new order
        $order_details  = [
            'table_no'    => $tableNo->table_no,
            'order_on'  => date('Y-m-d H:i:s'),
            'ordered_by'  => 'User',
            'status'      =>  0,
            'created_at'  => date('Y-m-d H:i:s'),
        ];
        $is_order_created = UserOrder::create($order_details);
        $orderId = $is_order_created->id;

        if (count($request->menuId) > 0) {
            foreach($request->menuId as $item=>$v) {
                $data = array(
                    'table_no' => $request->tableId,
                    'order_no' => 'Order'.$orderId.'',
                    'menu_id'  => $request->menuId[$item],
                    'quantity' => $request->quantity[$item],
                    'amount'   => 0.00,
                    'created_at'  => date('Y-m-d H:i:s')
                );

            $is_item_inserted = ItemOrdered::insert($data);
            }
        }

        if($is_order_created && $is_item_inserted)
        {
            $this->data = $is_order_created && $is_item_inserted;
            //order created, return success response
            return response()->json([
                'success'   => $this->success,
                'message'   => 'Order created successfully',
                'data'      => $this->data
            ], $this->code);
        }
        $this->success  = false;
        return response()->json([
            'success'   => $this->success,
            'message'   => 'Something went wrong',
            'data'      => $this->data
        ], $this->code);
    }

    public function getUserOrder(Request $request){
        $data       = $request->only('tableId');
        $validator  = Validator::make($data, [
            'tableId'      => 'required|exists:tables,id'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $tableOrder = ItemOrdered::where('item_ordereds.table_no', $request->tableId)
                      ->join('tables as t', 'item_ordereds.table_no', '=' , 't.id')
                      ->join('menus as m', 'item_ordereds.menu_id', '=' , 'm.id')
                      ->select('item_ordereds.table_no as tableId','t.table_no as tableNo','m.id as menuId','m.menu_name as menuName','m.menu_description as menuDescription','m.menu_price as menuPrice', 'item_ordereds.quantity as quantity')
                      ->get()
                      ->toArray();

        $order_array=array();
    if(count($tableOrder) > 0){
        $sum = 0;
        foreach($tableOrder as $key=>$order_value){
            $data = array(
                 'tableId'    => $order_value['tableId'],
                 'tableNo'    => $order_value['tableNo'],
                 'menuId'     => $order_value['menuId'],
                 'menuName'   => $order_value['menuName'],
                 'menuDescription' => $order_value['menuDescription'],
                 'menuPrice'  => $order_value['menuPrice'],
                 'quantity'   => $order_value['quantity'],
                 'order_price' =>  $order_value['menuPrice'] * $order_value['quantity'],
            );
        array_push($order_array , $data);
        }
    }
    $total_amount = array_sum(array_column($order_array, 'order_price'));
    if($order_array){
            $this->data['order_data'] = $order_array;
            $this->data['total_amount'] =  $total_amount ;

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
            'message'   => 'Something went wrong',
            'data'      => $this->data
            ], $this->code);
    }















}
