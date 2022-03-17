<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use DataTables;
use Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Session;
use Illuminate\Support\Facades\Crypt;


class TableController extends Controller
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

    public function tableList(Request $request)
    {   
        if ($request->ajax()) {
            $data   = Table::all();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('image', function ($data){ 
                        $url=asset($data->image); 
                        return '<img src='.$url.' border="0" height="100" width="100" class="img-rounded" align="center" />'; 
                    })
                    ->addColumn('status',function ($data){
                        return
                        (($data->status == 1) ? "Available" : "Reserved" );
                   })
                    ->addColumn('action',function ($data){
                        $b_url = \URL::to('/');
                       
                        return "<a href='$data->image' download ='$data->image' class='btn btn-xs btn-success'>Download QR</a>
                        <button onclick=deleteTable(".$data->id.") class='btn btn-xs btn-danger'><i class='fas fa-trash'></i></button>";
                    })
                    ->rawColumns(['image','status','action'])
                    ->make(true);
        }
        return view('admin.pages.table-list');
    }

    public function tableAddView()
    {
        return view('admin.pages.table-add');
    }

    public function tableAdd(Request $request){
        $rules = [
			'table_data' => 'required|string|max:255',
            'table_no'   => 'required|string|max:255',
        ];
        $this->validate($request, $rules);
        $time = time();
        $qrImage = QrCode::size(500)
                   ->generate($request->table_data, 'public/qr_images/'.$time.'.svg');
        $imgName = 
        $img_url = 'public/qr_images/'.$time.'.svg';

        $table_details = [
            'table_name' => $request->table_data,
            'table_no'   => $request->table_no,
            'image'      => $img_url, 
            'status'     => '1',
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $is_inserted = Table::insert($table_details);
        if($is_inserted)
        {
            $request->session()->flash('message', 'Table QR data created successfully!'); 
        }
        else
        {
            $request->session()->flash('message', ' failed!'); 
        }
        $request->session()->flash('alert-class', 'alert-success'); 
        return redirect(route('table-list'));
    }

    public function tableDelete(Request $request)
    {
        $table_details   = Table::find($request->id);
        if($table_details == null)
        {
            return response()->json([
                'status'    => 'error',
                'message'   => "Something Went Wrong"
            ]);
        }
        $is_delete   = Table::where('id',$request->id)
                       ->delete();
        if($is_delete == 1)
        {
            return response()->json([
                'status'    => 'success',
                'message'   => "Table Deleted Successfully"
            ]);
        }
        return response()->json([
            'status'    => 'error',
            'message'   => "Something Went Wrong"
        ]);
    }

    public function tableAvailability(Request $request){
        if ($request->ajax()) {
            $data   = Table::all();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('status',function ($data){
                        return
                        (($data->status == 1) ? "Available" : "Reserved" );
                   })
                   ->addColumn('action',function ($data){
                    $b_url = \URL::to('/');
                    return
                    "<a href=".$b_url."/table-availability/".Crypt::encrypt($data->id)."/edit class='btn btn-xs btn-primary'><i class='fa fa-edit'></i></a>";
                   
                })
                ->rawColumns(['status','action'])
                ->make(true);
        }
        return view('admin.pages.table-availability');

    }

    public function tableAvailabilityEdit(Request $request){
        $decrypted_id  = Crypt::decrypt($request->id);
        $table_details  = Table::find($decrypted_id);
        return view('admin.pages.table-availability-edit', compact(['table_details']));  
    }

    public function tableAvailabilityUpdate(Request $request){
        $table_details = Table::find($request->id);
        $rules = ([
                    'table_no'        => 'required|string|max:255',
                    'status' => 'required|string|max:255',
                ]);

        $this->validate($request, $rules);
        $is_updated = Table::where('id', $request->id)
                            ->update([
                                'table_no'  => $request->table_no,
                                'status'    => $request->status,
                                ]);

        if($is_updated){
            $request->session()->flash('message', 'Table availability successfully!'); 
        } else{
            $request->session()->flash('message', ' failed!'); 
        }
        $request->session()->flash('alert-class', 'alert-success'); 
        return redirect(route('table-availability'));
        
    }


}
