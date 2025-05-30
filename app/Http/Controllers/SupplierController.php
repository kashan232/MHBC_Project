<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use App\Models\SupplierPayment;
use App\Models\VendorPayment;
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
            $userId = Auth::id();

            $supplier = Supplier::create([
                'admin_or_user_id' => $userId,
                'name'             => $request->name,
                'mobile'           => $request->mobile,
                'company_name'     => $request->company_name,
                'address'          => $request->address,
                'created_at'       => Carbon::now(),
                'updated_at'       => Carbon::now(),
            ]);

            // Vendor Ledger Create (One-time Opening Balance)
            SupplierLedger::create([
                'admin_or_user_id' => $userId,
                'supplier_id'        => $supplier->name,
                'opening_balance'  => $request->opening_balance,
                'previous_balance' => $request->opening_balance,
                'closing_balance'  => $request->opening_balance,
                'created_at'       => Carbon::now(),
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

    public function supplier_ledger()
    {
        if (Auth::id()) {
            $userId = Auth::id();
            $SupplierLedgers = SupplierLedger::where('admin_or_user_id', $userId)->with('supplier')->get();
            return view('admin_panel.supplier.supplier_ledger', compact('SupplierLedgers'));
        } else {
            return redirect()->back();
        }
    }

    public function supplier_payments()
    {
        if (Auth::id()) {
            $userId = Auth::id();
            // dd($userId);
            $Suppliers = Supplier::where('admin_or_user_id', '=', $userId)->get();
            return view('admin_panel.supplier.supplier_payments', [
                'Suppliers' => $Suppliers
            ]);
        } else {
            return redirect()->back();
        }
    }

    public function getsupplierbalance($id)
    {
        $balance = SupplierLedger::where('supplier_id', $id)->value('closing_balance');
        $purchases = Purchase::where('supplier', $id)
            ->select('purchase_date', 'Payable_amount')
            ->orderBy('purchase_date', 'desc')
            ->get();

        return response()->json([
            'balance' => $balance ?? 0,
            'purchases' => $purchases
        ]);
    }

    public function supplierpaymentstore(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'detail' => 'nullable|string|max:255',
        ]);

        $vendorId = $request->supplier_id;

        // Get last ledger entry for this vendor
        $latestLedger = SupplierLedger::where('supplier_id', $vendorId)->latest()->first();

        $previousBalance = $latestLedger ? $latestLedger->closing_balance : 0;
        $newClosing = $previousBalance - $request->amount;

        if ($latestLedger) {
            $latestLedger->update([
                'closing_balance' => $newClosing,
            ]);
            $ledgerId = $latestLedger->id;
        } else {
            $newLedger = SupplierLedger::create([
                'admin_or_user_id' => auth()->id(),
                'supplier_id' => $vendorId,
                'previous_balance' => $previousBalance,
                'closing_balance' => $newClosing,
            ]);
            $ledgerId = $newLedger->id;
        }

        // Save the payment
        SupplierPayment::create([
            'admin_or_user_id' => auth()->id(),
            'supplier_id' => $vendorId,
            'amount_paid' => $request->amount,
            'payment_date' => $request->date,
            'description' => $request->detail,
        ]);

        return redirect()->back()->with('success', 'Supplier payment saved successfully.');
    }

    public function amountpaidsupplier()
    {
        if (Auth::id()) {
            $userId = Auth::id();
            $supplierPayments = SupplierPayment::where('admin_or_user_id', $userId)->with('supplier')->get();
            return view('admin_panel.supplier.amountpaidsupplier', compact('supplierPayments'));
        } else {
            return redirect()->back();
        }
    }
}
