<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
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
        $menus = Menu::all();
        return $this->sendResponse(MenuResource::collection($menus), 'Menus retrieved successfully.');

    }

    public function listTables(){
        $tables = Table::where('status', 1)
                  ->select('table_name','table_no')
                  ->get();
        return $this->sendResponse(TableResource::collection($tables), 'Tables retrieved successfully.');

    }


}
