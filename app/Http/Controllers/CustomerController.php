<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\CustomerRecovery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function customer()
    {
        if (Auth::id()) {
            $userId = Auth::id();
            // dd($userId);
            $Customers = Customer::where('admin_or_user_id', $userId)
                ->leftJoin('customer_credits', 'customers.id', '=', 'customer_credits.customerId')
                ->select('customers.*', 'customer_credits.closing_balance')
                ->get();
            return view('admin_panel.customers.customers', [
                'Customers' => $Customers
            ]);
        } else {
            return redirect()->back();
        }
    }

    public function store_customer(Request $request)
    {
        if (Auth::id()) {
            $usertype = Auth()->user()->usertype;
            $userId = Auth::id();

            // Customer creation
            $customer = Customer::create([
                'admin_or_user_id'    => $userId,
                'customer_name'       => $request->customer_name,
                'customer_phone'      => $request->customer_phone,
                'customer_address'    => $request->customer_address,
                'created_at'          => Carbon::now(),
                'updated_at'          => Carbon::now(),
            ]);

            // Insert opening balance into customer_credits table
            $opening_balance = $request->opening_balance;

            DB::table('customer_credits')->insert([
                'customerId'       => $customer->id,
                'customer_name'    => $request->customer_name,
                'previous_balance' => $opening_balance,
                'net_total'        => 0,
                'closing_balance'  => $opening_balance,
                'created_at'       => Carbon::now(),
                'updated_at'       => Carbon::now()
            ]);

            return redirect()->back()->with('success', 'Customer and Opening Balance created successfully');
        } else {
            return redirect()->back();
        }
    }
    public function update_customer(Request $request)
    {
        if (Auth::id()) {
            $usertype = Auth()->user()->usertype;
            $userId = Auth::id();
            // dd($request);
            $update_id = $request->input('customer_id');
            $name = $request->input('customer_name');
            $phone = $request->input('customer_phone');
            $address = $request->input('customer_address');

            Customer::where('id', $update_id)->update([
                'customer_name'          => $name,
                'customer_phone'          => $phone,
                'customer_address'          => $address,
                'updated_at' => Carbon::now(),
            ]);
            return redirect()->back()->with('success', 'Customer Updated Successfully');
        } else {
            return redirect()->back();
        }
    }

    public function processRecovery(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'recovery_date' => 'required|date',
            'recovery_amount' => 'required|numeric|min:0',
        ]);

        $customer_id = $request->customer_id;

        // Fetch the customer record (use first() to get a single record)
        $customer = CustomerCredit::where('customerId', $customer_id)->first();

        // Check if the customer record exists
        if (!$customer) {
            return redirect()->back()->withErrors(['customer_id' => 'Customer credit record not found!']);
        }

        // Calculate new balances
        $recoveryAmount = $request->recovery_amount;
        $updatedClosingBalance = $customer->closing_balance - $recoveryAmount;

        // Store recovery details in the CustomerRecovery table
        CustomerRecovery::create([
            'customer_id' => $customer->customerId, // Use customerId instead of id for CustomerCredit
            'customer_name' => $customer->customer_name,
            'recovery_date' => $request->recovery_date,
            'recovery_amount' => $recoveryAmount,
            'closing_balance' => $updatedClosingBalance,
        ]);

        // Update customer's balance in the CustomerCredit table
        $customer->previous_balance -= $recoveryAmount;
        $customer->closing_balance = $updatedClosingBalance;
        $customer->save();

        return redirect()->back()->with('success', 'Customer recovery details saved successfully!');
    }

    public function customer_recovires()
    {
        if (Auth::id()) {
            $userId = Auth::id();
            // dd($userId);
            // Fetch customers along with their closing balance from customer_credits
            $Customers = CustomerRecovery::get();

            return view('admin_panel.customers.customers_recoveries', [
                'Customers' => $Customers
            ]);
        } else {
            return redirect()->back();
        }
    }

    public function updateRecovery(Request $request)
    {
        $request->validate([
            'recovery_id' => 'required|exists:customer_recoveries,id',
            'recovery_date' => 'required|date',
            'adjustment_type' => 'required|in:plus,minus',
            'adjustment_amount' => 'required|numeric|min:0',
        ]);

        $recovery = CustomerRecovery::findOrFail($request->recovery_id);
        $oldAmount = $recovery->recovery_amount;
        $oldClosing = $recovery->closing_balance;

        $adjustmentType = $request->input('adjustment_type');
        $adjustmentAmount = $request->input('adjustment_amount');

        // Default to old values
        $newRecoveryAmount = $oldAmount;
        $newClosing = $oldClosing;

        if ($adjustmentType === 'plus') {
            $newRecoveryAmount += $adjustmentAmount;
            $newClosing -= $adjustmentAmount;
        } else {
            $newRecoveryAmount -= $adjustmentAmount;
            $newClosing += $adjustmentAmount;
        }

        $recovery->recovery_date = $request->recovery_date;
        $recovery->recovery_amount = $newRecoveryAmount;
        $recovery->closing_balance = $newClosing;
        $recovery->save();

        // Update customer credit (if applicable)
        $customerCredit = CustomerCredit::where('customerId', $recovery->customer_id)->first();
        if ($customerCredit) {
            $customerCredit->previous_balance = $newRecoveryAmount;
            $customerCredit->closing_balance = $newClosing;
            $customerCredit->save();
        }

        return redirect()->back()->with('success', 'Customer recovery updated successfully!');
    }



    public function addCredit(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'credit_amount' => 'required|numeric|min:0',
        ]);
        // Get customer credit entry if it exists
        $customerCredit = CustomerCredit::where('customerId', $request->customer_id)->first();

        $creditAmount = $request->credit_amount;
        $customer_name = $request->customer_name;

        if ($customerCredit) {
            // Update the existing entry if customer credit exists
            $customerCredit->previous_balance += $creditAmount;
            $customerCredit->closing_balance += $creditAmount;
            $customerCredit->save();
        } else {
            // Create a new entry if customer credit does not exist
            $customerCredit = CustomerCredit::create([
                'customerId' => $request->customer_id,
                'customer_name' => $customer_name,
                'previous_balance' => $creditAmount,
                'net_total' => '0',
                'closing_balance' => $creditAmount, // Assuming the balance starts with the credit amount
            ]);
        }

        return redirect()->back()->with('success', 'Credit added successfully to the customer.');
    }
}
