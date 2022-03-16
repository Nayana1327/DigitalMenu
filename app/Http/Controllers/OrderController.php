<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use DataTables;
use Response;
use App\Exports\OrderExport;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
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

    public function orderList(Request $request)
    {   
        if ($request->ajax()) {
            $data   = Order::all();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
        }
        return view('admin.pages.order-list');
    }

    public function reportView(Request $request)
    {   
        if ($request->ajax()) {
            $data   = Order::where('status', '3')
                      ->get();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('status',function ($data){
                        return
                        (($data->status == 3) ? "Paid" : "Cancelled" );
                   })
                    ->make(true);
        }
        return view('admin.pages.report');
    }

    public function exportReport(Request $request){

        return Excel::download(new OrderExport, 'Orders.xlsx');
    }
}
