<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController as BaseController;

use App\Models\Category;

class CategoryController extends BaseController
{
    /**
     * Listing all the categories from the databse.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $categories = Category::select('id as categoryId','category_name as categoryName')
                                ->get()
                                ->toArray();
                                
        if($categories){
            $this->data = $categories;  
            return response()->json([
                'success'       => $this->success,
                'message'       => 'Categories fetched successfully',
                'successData'   => $this->data
            ], $this->code['http_created']);
        }

        $this->success  = false;
        return response()->json([
            'success'   => $this->success,
            'message'   => 'No category found',
            'errorData' => $this->data
        ], $this->code['http_not_found']);
    }
}
