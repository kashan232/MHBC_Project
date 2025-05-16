<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function supplier()
    {
        if (Auth::id()) {
            $userId = Auth::id();
            // dd($userId);
            $Suppliers = Supplier::where('admin_or_user_id', '=', $userId)->get();
            return view('admin_panel.supplier.supplier', [
                'Suppliers' => $Suppliers
            ]);
        } else {
            return redirect()->back();
        }
    }

    public function store_supplier(Request $request)
    {
        if (Auth::id()) {
            $usertype = Auth()->user()->usertype;
            $userId = Auth::id();
            Supplier::create([
                'admin_or_user_id'    => $userId,
                'name'          => $request->name,
                'mobile'          => $request->mobile,
                'company_name'          => $request->company_name,
                'address'          => $request->address,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]);
            return redirect()->back()->with('success', 'Supplier Added Successfully');
        } else {
            return redirect()->back();
        }
    }
    public function update_supplier(Request $request)
    {
        if (Auth::id()) {
            $usertype = Auth()->user()->usertype;
            $userId = Auth::id();
            // dd($request);
            $update_id = $request->input('supplier_id');
            $name = $request->input('name');
            $email = $request->input('email');
            $mobile = $request->input('mobile');
            $company_name = $request->input('company_name');
            $address = $request->input('address');

            Supplier::where('id', $update_id)->update([
                'admin_or_user_id'    => $userId,
                'name'          => $name,
                'mobile'          => $mobile,
                'company_name'    => $company_name,
                'address'          => $address,
                'updated_at' => Carbon::now(),
            ]);
            return redirect()->back()->with('success', 'Supplier Updated Successfully');
        } else {
            return redirect()->back();
        }
    }
}
