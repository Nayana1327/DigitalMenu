<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use DataTables;
use Response;

class CategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function categoryList(Request $request)
    {   
        if ($request->ajax()) {
            $data   = Category::all();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action',function ($data){
                        $b_url = \URL::to('/');
                        return 
                        "<button onclick=deleteCategory(".$data->id.") class='btn btn-xs btn-danger'><i class='fas fa-trash'></i></button>";
                    })
                    ->make(true);
        }
        return view('admin.pages.category-list');
    }

    public function categoryAddView()
    {
        return view('admin.pages.category-add');
    }

    public function categoryAdd(Request $request){
        $rules = [
			'category_name' => 'required|string|max:255'
		];
        $this->validate($request, $rules);
        $category_details = [
            'category_name' => $request->category_name,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $is_inserted = Category::insert($category_details);
        if($is_inserted)
        {
            $request->session()->flash('message', 'Category created successfully!'); 
        }
        else
        {
            $request->session()->flash('message', ' failed!'); 
        }
        $request->session()->flash('alert-class', 'alert-success'); 
        return redirect(route('category-list'));
    }

    public function categoryDelete(Request $request)
    {
        $category_details   = Category::find($request->id);
        if($category_details == null)
        {
            return response()->json([
                'status'    => 'error',
                'message'   => "Something Went Wrong"
            ]);
        }
        $is_delete   = Category::where('id',$request->id)
                       ->delete();
        if($is_delete == 1)
        {
            return response()->json([
                'status'    => 'success',
                'message'   => "Category Deleted Successfully"
            ]);
        }
        return response()->json([
            'status'    => 'error',
            'message'   => "Something Went Wrong"
        ]);
    }
}
