<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Cuisine;
use App\Models\Portion;
use DataTables;
use Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;


class MenuController extends Controller
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
   

    public function menuList(Request $request)
    {   
        if ($request->ajax()) {
            $data   = Menu::all();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('menu_image', function ($data){ 
                        $url=asset("storage/menu_item_images/$data->menu_image"); 
                        return '<img src='.$url.' border="0" height="100" width="100" class="img-rounded" align="center" />'; 
                    })
                    ->addColumn('menu_status',function ($data){
                        $b_url = \URL::to('/');
                        return
                        (($data->menu_status == 1)?
                                    "<button onclick=unactivateMenu(".$data->id.") class='btn btn-xs btn-success'><i class='fa fa-check'></i></button>"
                                    :
                                    "<button onclick=activateMenu(".$data->id.") class='btn btn-xs btn-warning'><i class='fa fa-times'></i></button>"
                        );
                   })
                    ->addColumn('action',function ($data){
                        $b_url = \URL::to('/');
                        return
                        "<a href=".$b_url."/menu/".Crypt::encrypt($data->id)."/edit class='btn btn-xs btn-primary'><i class='fa fa-edit'></i></a>
                        <button onclick=deleteMenu(".$data->id.") class='btn btn-xs btn-danger'><i class='fa fa-trash'></i></button>";
                    })
                    ->rawColumns(['menu_image','menu_status','action'])
                    ->make(true);
        }
        return view('admin.pages.menu-list');
    }

    public function menuUnactivate(Request $request)
    { 
        $menu_details  = Menu::find($request->id);
        $is_unactivated = Menu::where('id', $request->id)
                        ->update([
                            'menu_status'      => '0'
                        ]);
        if($is_unactivated == 1)
        {
            return response()->json([
                'status'    => 'success',
                'message'   => "Item UnActivated Successfully"
            ]);
        }
        return response()->json([
            'status'    => 'error',
            'message'   => "Something Went Wrong"
        ]);
    }

    public function menuActivate(Request $request)
    { 
        $menu_details   = Menu::find($request->id);
        $is_activated = Menu::where('id', $request->id)
                        ->update([
                            'menu_status'      => '1'
                        ]);
        if($is_activated == 1)
        {
            return response()->json([
                'status'    => 'success',
                'message'   => "Item Activated Successfully"
            ]);
        }
        return response()->json([
            'status'    => 'error',
            'message'   => "Something Went Wrong"
        ]);
    }
    

    public function menuAddView()
    { 
        $category = Category::all();
        $cuisine = Cuisine::all();
        $portion = Portion::all();
        
        return view('admin.pages.menu-add', compact(['category', 'cuisine', 'portion']));
    }

    public function menuAdd(Request $request){
        $rules = [
			'menu_name'        => 'required|string|max:255',
            'menu_description' => 'required|string|max:255',
            'menu_category'    => 'required|string|max:255',
            'menu_cuisine'     => 'required|string|max:255',
            'menu_portion'     => 'required|string|max:255',
            'menu_price'       => 'required|string|max:255',
            'menu_file_name'   => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'sub_category'     => 'required|string|max:255'
        ];
        $this->validate($request, $rules);
        
        //1.get file name wth ext.
        $fileNameWithExt = $request->file('menu_file_name')->getClientOriginalName();
        //2.get just file name
        $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
        //1.get just ext.
        $extension = $request->file('menu_file_name')->getClientOriginalExtension();
        //4.file name to store
        $fileNameToStore = $fileName.'_'.time().'.'.$extension;

        //upload image
        $path = $request->file('menu_file_name')->storeAs('public/menu_item_images', $fileNameToStore);
        

        $menu_details = [
            'menu_name'        => $request->menu_name,
            'menu_description' => $request->menu_description,
            'menu_category'    => $request->menu_category,
            'menu_cuisine'     => $request->menu_cuisine,
            'menu_portion'     => $request->menu_portion,
            'menu_price'       => $request->menu_price,
            'menu_image'       => $fileNameToStore,
            'menu_status'      => '1',
            'created_at'       => date('Y-m-d H:i:s'),
            'sub_category'     => $request->sub_category,
        ];
        $is_inserted = Menu::insert($menu_details);
        if($is_inserted)
        {
            $request->session()->flash('message', 'Food Item created successfully!'); 
        }
        else
        {
            $request->session()->flash('message', ' failed!'); 
        }
        $request->session()->flash('alert-class', 'alert-success'); 
        return redirect(route('menu-list'));
    }

    public function menuEdit(Request $request){
        $decrypted_id  = Crypt::decrypt($request->id);
        $menu_details  = Menu::find($decrypted_id);
        $category = Category::all();
        $cuisine = Cuisine::all();
        $portion = Portion::all();
        
        return view('admin.pages.menu-edit', compact(['menu_details','category','cuisine','portion']));  
    }

    public function menuUpdate(Request $request){
        $menu_details = Menu::find($request->id);
        $rules = ([
                    'menu_name'        => 'required|string|max:255',
                    'menu_description' => 'required|string|max:255',
                    'menu_category'    => 'required|string|max:255',
                    'menu_cuisine'     => 'required|string|max:255',
                    'menu_portion'     => 'required|string|max:255',
                    'menu_price'       => 'required|string|max:255',
                    'menu_file_name'   => 'required|image|mimes:jpeg,png,jpg,gif,svg',
                    'sub_category'     => 'required|string|max:255'
                ]);

        $this->validate($request, $rules);

        if($request->hasFile('menu_file_name')){
            //1.get file name wth ext.
            $fileNameWithExt = $request->file('menu_file_name')->getClientOriginalName();
            //2.get just file name
            $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            //1.get just ext.
            $extension = $request->file('menu_file_name')->getClientOriginalExtension();
            //4.file name to store
            $fileNameToStore = $fileName.'_'.time().'.'.$extension;

            //upload image
            $path = $request->file('menu_file_name')->storeAs('public/menu_item_images', $fileNameToStore);

            if($menu_details->menu_image != 'noImage.png'){
                Storage::delete('public/menu_item_images/'.$menu_details->menu_image);
            }
            
            $menu_details->menu_image = $fileNameToStore;
        }


        $is_updated = Menu::where('id', $request->id)
                            ->update([
                                'menu_name'        => $request->menu_name,
                                'menu_description' => $request->menu_description,
                                'menu_category'    => $request->menu_category,
                                'menu_cuisine'     => $request->menu_cuisine,
                                'menu_portion'     => $request->menu_portion,
                                'menu_price'       => $request->menu_price,
                                'menu_image'       => $fileNameToStore,
                                'sub_category'     => $request->sub_category,
                                ]);

        if($is_updated){
            $request->session()->flash('message', 'Food Item edited successfully!'); 
        } else{
            $request->session()->flash('message', ' failed!'); 
        }
        $request->session()->flash('alert-class', 'alert-success'); 
        return redirect(route('menu-list'));
        
    }

    public function menuDelete(Request $request)
    {
        $menu_details   = Menu::find($request->id);
        if($menu_details == null)
        {
            return response()->json([
                'status'    => 'error',
                'message'   => "Something Went Wrong"
            ]);
        }
        $is_delete   = Menu::where('id',$request->id)
                       ->delete();
        if($is_delete == 1)
        {
            return response()->json([
                'status'    => 'success',
                'message'   => "Menu Deleted Successfully"
            ]);
        }
        return response()->json([
            'status'    => 'error',
            'message'   => "Something Went Wrong"
        ]);
    }
}
