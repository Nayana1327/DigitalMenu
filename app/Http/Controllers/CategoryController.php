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
                    ->addColumn('category_image', function ($data){
                        $url=asset("storage/category_images/$data->category_image");
                        return '<img src='.$url.' border="0" height="100" width="100" class="img-rounded" align="center" />';
                    })
                    ->addColumn('action',function ($data){
                        $b_url = \URL::to('/');
                        return 
                        "<button onclick=deleteCategory(".$data->id.") class='btn btn-xs btn-danger'><i class='fas fa-trash'></i></button>";
                    })
                    ->rawColumns(['category_image','action'])
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
			'category_name'         => 'required|string|max:255',
            'category_file_name'    => 'required|image|mimes:jpeg,png,jpg,gif,svg'
		];
        $this->validate($request, $rules);
        $time = time();
        //1.get file name wth ext.
        $fileNameWithExt = $request->file('category_file_name')->getClientOriginalName();
        //2.get just file name
        $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
        //1.get just ext.
        $extension = $request->file('category_file_name')->getClientOriginalExtension();
        //4.file name to store
        $fileNameToStore = $time.'.'.$extension;

        //upload image
        $path = $request->file('category_file_name')->storeAs('category_images', $fileNameToStore);

        // $imageName = 'public/menu_item_images/'.$request->menu_name.'_'.time().'.'.$extension.'';

        $category_details = [
            'category_name'     => $request->category_name,
            'category_image'    => $fileNameToStore,
            'created_at'        => date('Y-m-d H:i:s'),
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
