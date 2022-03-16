<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Category;
use App\Models\Menu;
use Validator;
use App\Http\Resources\Category as CategoryResource;
use App\Http\Resources\Menu as MenuResource;

class ApiController extends BaseController
{
    public function listCategories(){
       $categories = Category::all();
       return $this->sendResponse(CategoryResource::collection($categories), 'Categories retrieved successfully.');

    } 

    public function listMenus(){
        $menus = Menu::all();
        return $this->sendResponse(MenuResource::collection($menus), 'Menu retrieved successfully.');

    }
}
