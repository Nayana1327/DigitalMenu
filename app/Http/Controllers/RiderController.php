<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Models\Rider;
use DataTables;
use Response;

class RiderController extends Controller
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

    public function riderList(Request $request)
    {   
        if ($request->ajax()) {
            $data   = Rider::all();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action',function ($data){
                        $b_url = \URL::to('/');
                        return 
                        "<button onclick=deleteRider(".$data->id.") class='btn btn-xs btn-danger'><i class='fas fa-trash'></i></button>
                         ";
                    })
                    ->make(true);
        }
        return view('admin.pages.rider-list');
    }

    public function riderAddView()
    { 
        return view('admin.pages.rider-add');
    }

    public function riderAdd(Request $request){
        $rules = [
            'rider_name'      => 'required',
            'email'     => 'required|unique:riders,email',
            'password'  => 'required|min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*)(=+\/\\~`-]).{8,20}$/'
        ];
        $messages = [
            'password.regex' => 'This Password is invalid! Password must contains minimum 8 characters with at least one uppercase letter, one lowercase letter, one number and one special character',
        ];
        $this->validate($request, $rules,$messages);
        $rider_details = [
            'rider_name'           => $request->rider_name,
            'email'                 => $request->email,
            'password'              => $request->password
        ];
        $is_inserted = Rider::insert($rider_details);
        
        if($is_inserted)
        {
            $request->session()->flash('message', 'New Delivery Person added successfully!'); 
        }
        else
        {
            $request->session()->flash('message', 'Delivery Person added failed!'); 
        }
        $request->session()->flash('alert-class', 'alert-success'); 
        return redirect(route('rider-list'));
    }

    public function riderDelete(Request $request)
    {
        $rider_details   = Rider::find($request->id);
        if($rider_details == null)
        {
            return response()->json([
                'status'    => 'error',
                'message'   => "Something Went Wrong"
            ]);
        }
        $is_delete   = Rider::where('id',$request->id)->delete();
        if($is_delete == 1)
        {
            return response()->json([
                'status'    => 'success',
                'message'   => "Delivery Person Deleted Successfully"
            ]);
        }
        return response()->json([
            'status'    => 'error',
            'message'   => "Something Went Wrong"
        ]);
    } 
}
