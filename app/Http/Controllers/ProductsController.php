<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use Illuminate\Support\Facades\Hash;
use AmrShawky\LaravelCurrency\Facade\Currency;
use Datatables;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
            if (request()->ajax()) {
                return datatables()->of(Products::select('id', 'title', 'image','description','currency','price','in_stock','status','created_at'))
                    ->editColumn('created_at', function ($request) {
                        return $request->created_at->format('d-m-Y H:i'); // format date time
                    })
                    ->filter(function($query) use ($request){
                        if (!empty($request->searchGender)) {
                            $query->where('currency', '=', $request->searchGender);
                        }
                        if (!empty($request->searchNameEmail)) {
                            $query->where('name','like','%'.$request->searchNameEmail.'%')
                            ->orWhere('email','like','%'.$request->searchNameEmail.'%');
                        }
                    })
                    ->addColumn('action', 'products.product-action')
                    ->rawColumns(['action'])
                    ->addIndexColumn()
                    ->make(true);
            }
            return view('products.products');
    }

    public function store(Request $request)
    {
        $product_id = $request->id;
        $imageName = '';
        $request->validate([
            // 'category_id' => 'required',
            'title' => 'required',
            'image' => 'required',
            'description' => 'required',
            'price' => 'required',
            'currency' => 'required',
            'in_stock' => 'required',
            'status' => 'required',
        ]);
        if ($request->hasFile('image')) {
            $image_file_extention = $request['image']->getClientOriginalExtension();
            $image_file_name = time().rand(99,999).'product_image.'.$image_file_extention;
            $move_file_path3 = $request['image']->move(public_path().'/uploads/product_image/',$image_file_name);
            $image_file_path = '/uploads/product_image/'.$image_file_name;
            $imageName = $image_file_name;
        }
        
        if($request->currency == 'INR'){
            $new_price = Currency::convert()->to('INR')->amount($request->currency)->get();
        }elseif ($request->currency == 'USD') {
            $new_price = Currency::convert()->to('USD')->amount($request->currency)->get();
        }else{
            $new_price = Currency::convert()->to('EURO')->amount($request->currency)->get();
        }
        $customer = Products::updateOrCreate(
            [
                'id' => $product_id
            ],
            [
                'category_id' => $request->category_id ?? '1',
                'title' => $request->title ?? '',
                'price' => $new_price ?? '',
                'currency' =>  $request->currency ?? '',
                'image' => $imageName ?? '',
                'description' => $request->description ?? '',
                'in_stock' => $request->in_stock ?? '',
                'status' => $request->status ?? '',
            ]
        );

        return Response()->json($customer);
    }


    public function edit(Request $request)
    {
        $where = array('id' => $request->id);
        $customer  = Products::where($where)->first();

        return Response()->json($customer);
    }

    public function destroy(Request $request)
    {
        $customer = Products::where('id', $request->id)->delete();

        return Response()->json($customer);
    }
}
