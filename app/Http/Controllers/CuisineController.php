<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuisine;
use DataTables;
use Response;

class CuisineController extends Controller
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

    public function cuisineList(Request $request)
    {   
        if ($request->ajax()) {
            $data   = Cuisine::all();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action',function ($data){
                        $b_url = \URL::to('/');
                        return 
                        "<button onclick=deleteCuisine(".$data->id.") class='btn btn-xs btn-danger'><i class='fas fa-trash'></i></button>";
                    })
                    ->make(true);
        }
        return view('admin.pages.cuisine-list');
    }

    public function cuisineAddView()
    {
        return view('admin.pages.cuisine-add');
    }

    public function cuisineAdd(Request $request){
        $rules = [
			'cuisine_name' => 'required|string|max:255'
		];
        $this->validate($request, $rules);
        $cuisine_details = [
            'cuisine_name' => $request->cuisine_name,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $is_inserted = Cuisine::insert($cuisine_details);
        if($is_inserted)
        {
            $request->session()->flash('message', 'Cuisine created successfully!'); 
        }
        else
        {
            $request->session()->flash('message', ' failed!'); 
        }
        $request->session()->flash('alert-class', 'alert-success'); 
        return redirect(route('cuisine-list'));
    }

    public function cuisineDelete(Request $request)
    {
        $cuisine_details   = Cuisine::find($request->id);
        if($cuisine_details == null)
        {
            return response()->json([
                'status'    => 'error',
                'message'   => "Something Went Wrong"
            ]);
        }
        $is_delete   = Cuisine::where('id',$request->id)
                       ->delete();
        if($is_delete == 1)
        {
            return response()->json([
                'status'    => 'success',
                'message'   => "Cuisine Deleted Successfully"
            ]);
        }
        return response()->json([
            'status'    => 'error',
            'message'   => "Something Went Wrong"
        ]);
    }
}
