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

class ApiController extends BaseController
{
    public function listCategories(){
       $categories = Category::select('id as categoryId','category_name as categoryName')
                     ->get();
       return $this->sendResponse(CategoryResource::collection($categories), 'Categories retrieved successfully.');

    } 

    public function listMenus(){
        $menus = Menu::where('menu_status', 1)
                 ->select('id as menuId','menu_name as menuName','menu_description as menuDescription','menu_category as menuCategory','menu_cuisine as menuCuisine','menu_portion as menuPortion','menu_price as menuPrice','menu_image as menuImage','sub_category as subCategory')
                 ->get();
        return $this->sendResponse(MenuResource::collection($menus), 'Menus retrieved successfully.');

    }

    public function listTables(){
        $tables = Table::where('status', 1)
                  ->select('id as tableId','table_name as tableQRData','table_no as tableNo.')
                  ->get();
        return $this->sendResponse(TableResource::collection($tables), 'Tables retrieved successfully.');

    }
    public function searchByMenu(Request $request){
        $request->categoryName = 'Lunch';
        $menus = Menu::where('menu_category', $request->categoryName)
                       ->orWhere('sub_category', $request->subCategory)
                       ->orWhere('menu_name', 'LIKE', '%'.$request->search.'%')
                       ->select('id as menuId','menu_name as menuName','menu_description as menuDescription','menu_category as menuCategory','menu_cuisine as menuCuisine','menu_portion as menuPortion','menu_price as menuPrice','menu_image as menuImage','sub_category as subCategory')
                       ->get();
        
        return $this->sendResponse(MenuResource::collection($menus), 'Menu Item retrieved successfully.');
        
    }



    




}
