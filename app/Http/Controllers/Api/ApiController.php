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
       $categories = Category::all();
       return $this->sendResponse(CategoryResource::collection($categories), 'Categories retrieved successfully.');

    } 

    public function listMenus(){
        $menus = Menu::where('menu_status', 1)
                 ->select('menu_name','menu_description','menu_category','menu_cuisine','menu_portion','menu_price','menu_image','sub_category')
                 ->get();
        return $this->sendResponse(MenuResource::collection($menus), 'Menus retrieved successfully.');

    }

    public function listTables(){
        $tables = Table::where('status', 1)
                  ->select('table_name','table_no')
                  ->get();
        return $this->sendResponse(TableResource::collection($tables), 'Tables retrieved successfully.');

    }
    // public function searchByMenu(Request $request){
        
    // }

    // $search = "Har";
  
    //     $users = User::select("*")
    //                     ->where('first_name', 'LIKE', '%'.$search.'%')
    //                     ->orWhere('last_name', 'LIKE', '%'.$search.'%')
    //                     ->orWhere('email', 'LIKE', '%'.$search.'%')
    //                     ->get();
  
    //     dd($users);




}
