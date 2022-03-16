<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Portion;
use DataTables;
use Response;

class PortionController extends Controller
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

    public function portionList(Request $request)
    {   
        if ($request->ajax()) {
            $data   = Portion::all();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action',function ($data){
                        $b_url = \URL::to('/');
                        return 
                        "<button onclick=deletePortion(".$data->id.") class='btn btn-xs btn-danger'><i class='fas fa-trash'></i></button>";
                    })
                    ->make(true);
        }
        return view('admin.pages.portion-list');
    }

    public function portionAddView()
    {
        return view('admin.pages.portion-add');
    }

    public function portionAdd(Request $request){
        $rules = [
			'portion_name' => 'required|string|max:255'
		];
        $this->validate($request, $rules);
        $portion_details = [
            'portion_name' => $request->portion_name,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $is_inserted = Portion::insert($portion_details);
        if($is_inserted)
        {
            $request->session()->flash('message', 'Portion created successfully!'); 
        }
        else
        {
            $request->session()->flash('message', ' failed!'); 
        }
        $request->session()->flash('alert-class', 'alert-success'); 
        return redirect(route('portion-list'));
    }

    public function portionDelete(Request $request)
    {
        $portion_details   = Portion::find($request->id);
        if($portion_details == null)
        {
            return response()->json([
                'status'    => 'error',
                'message'   => "Something Went Wrong"
            ]);
        }
        $is_delete   = Portion::where('id',$request->id)
                       ->delete();
        if($is_delete == 1)
        {
            return response()->json([
                'status'    => 'success',
                'message'   => "Portion Deleted Successfully"
            ]);
        }
        return response()->json([
            'status'    => 'error',
            'message'   => "Something Went Wrong"
        ]);
    }
}
