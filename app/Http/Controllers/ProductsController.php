<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Category;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
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
            'category_id' => 'category_id',
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

        $customer = Products::updateOrCreate(
            [
                'id' => $product_id
            ],
            [
                'category_id' => $request->category_id ?? '1',
                'title' => $request->title ?? '',
                'price' => $request->price?? '',
                'currency' =>  $request->currency ?? '',
                'category_id' => $request->category_id ?? '',
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

    public function load_product_list(Request $request){
        return view('products.product-list');
    }
    public function product_index(Request $request){
        try {
            $searchParams = $request->all();
            $products = Products::query()->where('status',"=",1)->where('in_stock',1)->get();
            if(isset($searchParams['searchCategory']) && $searchParams['searchCategory'] != '' || $searchParams['searchCategory'] != null){
                $products = Products::where('status',"=",1)->where('category_id','=',$searchParams['searchCategory'])->where('in_stock',1)->get();
            }elseif(isset($searchParams['searchProduct']) && $searchParams['searchProduct'] != '' || $searchParams['searchProduct'] != null){
                $products = Products::where('title','like','%'.$searchParams['searchProduct'].'%')->get();
            }
            if(isset($searchParams['searchCurrency']) && $searchParams['searchCurrency'] != null || $searchParams['searchCurrency'] != ''){
                foreach ($products as $product){
                    $formattedValue = '';
                    if($searchParams['searchCurrency'] == 'USD'){
                        $inrValue = $product->price;
                        $conversionRate = 0.012;
                        $usdValue = $inrValue * $conversionRate;
                        $formattedValue = self::asDollars($usdValue);
                        $product->price = $formattedValue;
                    }elseif ($searchParams['searchCurrency'] == 'EURO'){
                        $inrValue = $product->price;
                        $conversionRate = 0.011;
                        $euroValue = $inrValue * $conversionRate;
                        $formattedValue = self::asEURO($euroValue);
                        $product->price = $formattedValue;
                    }elseif ($searchParams['searchCurrency'] == 'INR'){
                        $formattedValue = self::asRupee($product->price);
                        $product->price = $formattedValue;
                    }
                }
            }else{
                foreach ($products as $product){
                $formattedValue = '';
                    if(Auth::user()->currency == 'USD'){
                        $inrValue = $product->price;
                        $conversionRate = 0.012;
                        $usdValue = $inrValue * $conversionRate;
                        $formattedValue = self::asDollars($usdValue);
                        $product->price = $formattedValue;
                    }elseif (Auth::user()->currency == 'EURO'){
                        $inrValue = $product->price;
                        $conversionRate = 0.011;
                        $euroValue = $inrValue * $conversionRate;
                        $formattedValue = self::asEURO($euroValue);
                        $product->price = $formattedValue;
                    }elseif (Auth::user()->currency == 'INR'){
                        $formattedValue = self::asRupee($product->price);
                        $product->price = $formattedValue;
                    }
                }
            }
            return Response()->json($products);
        }catch(\Exception $ex){
            dd($ex);
        }
    }
    function asRupee($value) {
        if ($value<0) return "-".asRupee(-$value);
        return '₹' . number_format($value, 2);
    }
    function asDollars($value) {
        if ($value<0) return "-".asDollars(-$value);
        return '$' . number_format($value, 2);
    }
    function asEURO($value) {
        if ($value<0) return "-".asEURO(-$value);
        return '€' . number_format($value, 2);
    }
}
