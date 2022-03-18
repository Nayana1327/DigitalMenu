<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Table;
use Validator;
use App\Http\Resources\Category as CategoryResource;
use App\Http\Resources\Menu as MenuResource;
use App\Http\Resources\Table as TableResource;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends BaseController
{
    //Category Listing Api
    public function listCategories(){
       $categories = Category::select('id as categoryId','category_name as categoryName')
                     ->get();
        if($categories){
            return $this->sendResponse(CategoryResource::collection($categories), 'Categories retrieved successfully.');
        }
        else{
            return $this->sendError('Categories not found');  
        }
    } 
    
    //Menu Listing Api By (CategoryName, Veg/Non-Veg, Search)
    public function listMenus(){
        $menus = Menu::where('menu_status', 1)
                 ->select('id as menuId','menu_name as menuName','menu_description as menuDescription','menu_category as menuCategory','menu_cuisine as menuCuisine','menu_portion as menuPortion','menu_price as menuPrice','menu_image as menuImage','sub_category as subCategory')
                 ->get();
        return $this->sendResponse(MenuResource::collection($menus), 'Menus retrieved successfully.');

    }

    //Available Table Listing Api
    public function listTables(){
        $tables = Table::where('status', 1)
                  ->select('id as tableId','table_name as tableQRData','table_no as tableNo')
                  ->get();
        if($tables){
            return $this->sendResponse(TableResource::collection($tables), 'Tables retrieved successfully.');
        }
        else{
                return $this->sendError('Tables not found');  
        }
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
    public function searchByMenu(Request $request){
        $categoryName = $request->categoryName;
        $subCategory = $request->subCategory;
        $searchValue = $request->searchValue;
        $menus = Menu::select('id as menuId','menu_name as menuName','menu_description as menuDescription','menu_category as menuCategory','menu_cuisine as menuCuisine','menu_portion as menuPortion','menu_price as menuPrice','menu_image as menuImage','sub_category as subCategory')
                      ->where('menu_status', 1) 
                      ->where(function($query) use ($categoryName, $subCategory) {
                            $query->where('menu_category', 'like', '%' .$categoryName. '%')
                            ->orWhere('sub_category', $subCategory);
                        })
                        ->where(function($query) use ($searchValue) {
                        $query->where('menu_name', 'like', '%'.$searchValue.'%')
                              ->orWhere('menu_description','like', '%'.$searchValue.'%');
                        })
                       ->get();
        if($menus){
            return $this->sendResponse(MenuResource::collection($menus), 'Menu Item retrieved successfully.');
        }  
        else{
        return $this->sendError('Menu not found');
        }
    
    }

   


    



    




}
