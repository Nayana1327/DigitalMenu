<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController as BaseController;
use Illuminate\Http\Request;

use App\Models\Menu;

class MenuController extends BaseController
{
    /**
     * Listing all the menu items from the databse.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $menus = Menu::where('menu_status', 1)
                        ->select('id as menuId','menu_name as menuName','menu_description as menuDescription','menu_category as menuCategory','menu_cuisine as menuCuisine','menu_portion as menuPortion','menu_price as menuPrice','menu_image as menuImage','sub_category as subCategory')
                        ->get()
                        ->toArray();

        if($menus){
            $this->data = $menus;
            return response()->json([
                'success'       => $this->success,
                'message'       => 'Menus fetched successfully',
                'successData'   => $this->data
            ], $this->code['http_created']);
        }

        $this->success  = false;
        return response()->json([
            'success'   => $this->success,
            'message'   => 'No menu items found',
            'errorData' => $this->data
        ], $this->code['http_not_found']);
    }

    /**
     * Search for menu items
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $categoryName   = $request->categoryName;
        $subCategory    = $request->subCategory;
        $searchValue    = $request->searchValue;

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
                       ->get()
                       ->toArray();

        foreach($menus as $key => $value){
            $menus[$key]['menuImage'] = asset('storage/menu_item_images/' . $value['menuImage']);
        }

        if($menus){
            $this->data = $menus;
            return response()->json([
            'success'       => $this->success,
            'message'       => 'Menus fetched successfully',
            'successData'   => $this->data
            ], $this->code['http_created']);
        }
        
            $this->success  = false;
            return response()->json([
            'success'   => $this->success,
            'message'   => 'No search found',
            'errorData' => $this->data
            ], $this->code['http_not_found']);
    }
}
