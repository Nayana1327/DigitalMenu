<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Models\Waiter;
use DataTables;
use Response;

class WaiterController extends Controller
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

    public function waiterList(Request $request)
    {   
        if ($request->ajax()) {
            $data   = Waiter::all();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action',function ($data){
                        $b_url = \URL::to('/');
                        return 
                        "<button onclick=deleteWaiter(".$data->id.") class='btn btn-xs btn-danger'><i class='fas fa-trash'></i></button>
                         ";
                    })
                    ->make(true);
        }
        return view('admin.pages.waiter-list');
    }

    public function waiterAddView()
    { 
        return view('admin.pages.waiter-add');
    }

    public function waiterAdd(Request $request){
        $rules = [
            'waiter_name'      => 'required',
            'email'     => 'required|unique:waiters,email',
            'password'  => 'required|min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*)(=+\/\\~`-]).{8,20}$/'
        ];
        $messages = [
            'password.regex' => 'This Password is invalid! Password must contains minimum 8 characters with at least one uppercase letter, one lowercase letter, one number and one special character',
        ];
        $this->validate($request, $rules,$messages);
        $waiter_details = [
            'waiter_name'           => $request->waiter_name,
            'email'                 => $request->email,
            'password'              => $request->password
        ];
        $is_inserted = Waiter::insert($waiter_details);
        
        if($is_inserted)
        {
            $request->session()->flash('message', 'New waiter added successfully!'); 
        }
        else
        {
            $request->session()->flash('message', 'Waiter added failed!'); 
        }
        $request->session()->flash('alert-class', 'alert-success'); 
        return redirect(route('waiter-list'));
    }

    public function waiterDelete(Request $request)
    {
        $waiter_details   = Waiter::find($request->id);
        if($waiter_details == null)
        {
            return response()->json([
                'status'    => 'error',
                'message'   => "Something Went Wrong"
            ]);
        }
        $is_delete   = Waiter::where('id',$request->id)
                       ->delete();
        if($is_delete == 1)
        {
            return response()->json([
                'status'    => 'success',
                'message'   => "Waiter Deleted Successfully"
            ]);
        }
        return response()->json([
            'status'    => 'error',
            'message'   => "Something Went Wrong"
        ]);
    } 
}
