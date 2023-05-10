<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers;
use Illuminate\Support\Facades\Hash;
use Datatables;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
            if (request()->ajax()) {
                return datatables()->of(Customers::select('id', 'name', 'email','phone','currency','role','status','created_at'))
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
                    ->addColumn('action', 'customers.customer-action')
                    ->rawColumns(['action'])
                    ->addIndexColumn()
                    ->make(true);
            }
            return view('customers.customers');
    }

    public function store(Request $request)
    {
        $customerId = $request->id;

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'currency' => 'required',
            'role' => 'required',
            'status' => 'required',
        ]);
        
        $customer = Customers::updateOrCreate(
            [
                'id' => $customerId
            ],
            [
                'name' => $request->name ?? '',
                'email' => $request->email ?? '',
                'phone' => $request->phone ?? '',
                'currency' => $request->currency ?? '',
                'password' => Hash::make($request->password) ?? '',
                'role' => $request->role ?? '',
                'status' => $request->status ?? '',
            ]
        );

        return Response()->json($customer);
    }


    public function edit(Request $request)
    {
        $where = array('id' => $request->id);
        $customer  = Customers::where($where)->first();

        return Response()->json($customer);
    }

    public function destroy(Request $request)
    {
        $customer = Customers::where('id', $request->id)->delete();

        return Response()->json($customer);
    }
}
