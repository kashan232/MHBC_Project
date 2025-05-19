<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // If using barryvdh/laravel-dompdf
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{

    public function Sale()
    {
        if (Auth::id()) {
            $userId = Auth::id();
            // dd($userId);
            $Purchases = Purchase::get();
            // dd($Purchases);
            return view('admin_panel.sale.sales', [
                'Purchases' => $Purchases,
            ]);
        } else {
            return redirect()->back();
        }
    }

    public function add_Sale()
    {
        if (Auth::id()) {
            $userId = Auth::id();
            // dd($userId);
            $Customers = Customer::get();
            $Warehouses = Warehouse::get();
            $Category = Category::get();

            // dd($Customers);
            return view('admin_panel.sale.add_sale', [
                'Customers' => $Customers,
                'Warehouses' => $Warehouses,
                'Category' => $Category,
            ]);
        } else {
            return redirect()->back();
        }
    }

    public function getItemsByCategory($categoryId)
    {
        $items = Product::where('category', $categoryId)->get(); // Adjust according to your database structure
        return response()->json($items);
    }


    public function view($id)
    {
        // Fetch the purchase details
        $purchase = Purchase::findOrFail($id);

        // Decode the JSON fields if necessary
        $purchase->item_category = json_decode($purchase->item_category);
        $purchase->item_name = json_decode($purchase->item_name);
        $purchase->quantity = json_decode($purchase->quantity);
        $purchase->price = json_decode($purchase->price);
        $purchase->total = json_decode($purchase->total);

        return view('admin_panel.purchase.view', [
            'purchase' => $purchase,
        ]);
    }

    public function store_Sale(Request $request)
    {
        $invoiceNo = Sale::generateInvoiceNo();
        \Log::info('Request Data:', $request->all());

        $discount = (float)($request->input('discount', 0));
        $totalPrice = (float)$request->input('total_price', 0);
        $payable_amount = (float) $request->input('payable_amount', 0);
        $cashReceived = (float) $request->input('cash_received', 0);
        $cashReturn = (float) $request->input('cash_return', 0);
        $saleType = $request->input('sale_type');

        $usertype = Auth()->user()->usertype;
        $userId = Auth::id();

        $itemNames = $request->input('item_name', []);
        $itemCategories = $request->input('item_category', []);
        $quantities = $request->input('quantity', []);

        // Step 1: Validate stock
        foreach ($itemNames as $key => $item_name) {
            $item_category = $itemCategories[$key] ?? '';
            $quantity = $quantities[$key] ?? 0;

            $product = Product::where('product_name', $item_name)
                ->where('category', $item_category)
                ->first();

            if (!$product) {
                return redirect()->back()->with('error', "Product $item_name in category $item_category not found.");
            }

            if ($product->stock < $quantity) {
                return redirect()->back()->with('error', "Insufficient stock for product $item_name.");
            }
        }

        // Customer info (only for credit)
        $customerId = null;
        $customerName = null;

        if ($saleType == 'credit') {
            $customerInfo = explode('|', $request->input('customer_info'));
            if (count($customerInfo) < 2) {
                return redirect()->back()->with('error', 'Invalid customer information format.');
            }

            $customerId = $customerInfo[0];
            $customerName = $customerInfo[1];

            $netTotal = $totalPrice - $discount;

            $customerCredit = CustomerCredit::where('customerId', $customerId)->first();
            if ($customerCredit) {
                $previousBalance = $customerCredit->previous_balance;
                $closingBalance = $previousBalance + $payable_amount;

                $customerCredit->net_total = $netTotal;
                $customerCredit->closing_balance = $closingBalance;
                $customerCredit->previous_balance = $closingBalance;
                $customerCredit->save();
            } else {
                CustomerCredit::create([
                    'customerId' => $customerId,
                    'customer_name' => $customerName,
                    'previous_balance' => $payable_amount,
                    'net_total' => $netTotal,
                    'closing_balance' => $payable_amount,
                ]);
            }
        } else {
            // For cash, use walk-in customer name
            $customerName = $request->input('cash_customer_name', 'Walk-in Customer');
        }

        // Prepare sale data
        $saleData = [
            'userid' => $userId,
            'user_type' => $usertype,
            'invoice_no' => $invoiceNo,
            'customerId' => $customerId,
            'customer' => $customerName,
            'sale_type' => $saleType,
            'sale_date' => $request->input('sale_date', ''),
            'warehouse_id' => $request->input('warehouse_id', ''),
            'item_category' => json_encode($request->input('item_category', [])),
            'item_name' => json_encode($request->input('item_name', [])),
            'unit' => json_encode($request->input('unit', [])),
            'quantity' => json_encode($request->input('quantity', [])),
            'price' => json_encode($request->input('price', [])),
            'total' => json_encode($request->input('total', [])),
            'note' => $request->input('note', ''),
            'total_price' => $totalPrice,
            'discount' => $discount,
            'scrap_amount' => $request->input('scrap_amount', 0),
            'Payable_amount' => $payable_amount,
        ];

        // Add cash fields only if cash sale
        if ($saleType == 'cash') {
            $saleData['cash_received'] = $cashReceived;
            $saleData['change_return'] = $cashReturn;
        }

        $sale = Sale::create($saleData);

        // Update stock
        foreach ($itemNames as $key => $item_name) {
            $item_category = $itemCategories[$key] ?? '';
            $quantity = $quantities[$key] ?? 0;

            $product = Product::where('product_name', $item_name)
                ->where('category', $item_category)
                ->first();

            if ($product) {
                $product->stock -= $quantity;
                $product->save();
            }
        }

        return redirect()->route('all-sales')->with('success', 'Sale recorded successfully');
    }



    // public function store_Sale(Request $request)
    // {
    //     $invoiceNo = Sale::generateInvoiceNo();
    //     \Log::info('Request Data:', $request->all());

    //     $discount = (float)($request->input('discount', 0));
    //     $totalPrice = (float)$request->input('total_price', 0);
    //     $cashReceived = (float)$request->input('cash_received', 0);
    //     $changeToReturn = (float)$request->input('change_to_return', 0);

    //     \Log::info('Processed Values:', [
    //         'discount' => $discount,
    //         'total_price' => $totalPrice,
    //         'cash_received' => $cashReceived,
    //         'change_to_return' => $changeToReturn,
    //     ]);

    //     $usertype = Auth()->user()->usertype;
    //     $userId = Auth::id();

    //     $itemNames = $request->input('item_name', []);
    //     $itemCategories = $request->input('item_category', []);
    //     $quantities = $request->input('quantity', []);

    //     // Step 1: Validate stock for all products
    //     foreach ($itemNames as $key => $item_name) {
    //         $item_category = $itemCategories[$key] ?? '';
    //         $quantity = $quantities[$key] ?? 0;

    //         $product = Product::where('product_name', $item_name)
    //             ->where('category', $item_category)
    //             ->first();

    //         if (!$product) {
    //             return redirect()->back()->with('error', "Product $item_name in category $item_category not found.");
    //         }

    //         if ($product->stock < $quantity) {
    //             return redirect()->back()->with('error', "Insufficient stock for product $item_name. Available: {$product->stock}, Required: $quantity.");
    //         }
    //     }

    //     // Get customer info from the concatenated string
    //     $customerInfo = explode('|', $request->input('customer_info'));
    //     if (count($customerInfo) < 2) {
    //         return redirect()->back()->with('error', 'Invalid customer information format.');
    //     }

    //     $customerId = $customerInfo[0];
    //     $customerName = $customerInfo[1];
    //     // Prepare data for storage
    //     $discount = (float) ($request->input('discount', 0));
    //     $totalPrice = (float) $request->input('total_price', 0);
    //     $netTotal = $totalPrice - $discount; // Calculate the net total amount

    //     // Get the existing customer credit to retrieve previous balance
    //     $customerCredit = CustomerCredit::where('customerId', $customerId)->first();

    //     $previous_balance = $request->input('previous_balance');
    //     $net_total = $request->input('net_total');
    //     $closing_balance = $request->input('closing_balance');
    //     $scrap_amount = $request->input('scrap_amount');
    //     $payable_amount = $request->input('payable_amount');
    //     // Initialize variables to hold balance details
    //     $previousBalance = 0;
    //     $closingBalance = 0;

    //     if ($customerCredit) {
    //         // If customer credit exists, get the previous balance
    //         $previousBalance = $customerCredit->previous_balance;

    //         // Update previous balance to include the new sale's payable amount
    //         $closingBalance = $previousBalance + $netTotal;

    //         // Update existing credit
    //         $customerCredit->net_total = $netTotal; // Store the net total from the current sale
    //         $customerCredit->closing_balance = $closingBalance; // Closing balance is now the updated previous balance
    //         $customerCredit->previous_balance = $closingBalance; // Update to the new previous balance
    //         $customerCredit->save();
    //     } else {
    //         // Create new credit entry for the customer
    //         CustomerCredit::create([
    //             'customerId' => $customerId,
    //             'customer_name' => $customerName,
    //             'previous_balance' => $netTotal, // Set previous balance to the net total for the first sale
    //             'net_total' => $netTotal, // This is the first sale's amount
    //             'closing_balance' => $netTotal, // Closing balance for first entry
    //         ]);

    //         // Set the balances for the first entry
    //         $previousBalance = 0; // No previous balance exists for new customers
    //         $closingBalance = $netTotal; // This will be the closing balance
    //     }

    //     // Step 2: Proceed to save the sale
    //     $saleData = [
    //         'userid' => $userId,
    //         'user_type' => $usertype,
    //         'invoice_no' => $invoiceNo,
    //         'customerId' => $customerId,
    //         'customer' => $customerName,
    //         'sale_date' => $request->input('sale_date', ''),
    //         'warehouse_id' => $request->input('warehouse_id', ''),
    //         'item_category' => json_encode($request->input('item_category', [])),
    //         'item_name' => json_encode($request->input('item_name', [])),
    //         'unit' => json_encode($request->input('unit', [])),
    //         'quantity' => json_encode($request->input('quantity', [])),
    //         'price' => json_encode($request->input('price', [])),
    //         'total' => json_encode($request->input('total', [])),
    //         'note' => $request->input('note', ''),
    //         'total_price' => $totalPrice,
    //         'discount' => $discount,
    //         'scrap_amount' => $scrap_amount,
    //         'Payable_amount' => $payable_amount,
    //     ];

    //     $sale = Sale::create($saleData);

    //     // Step 3: Deduct stock after successfully saving the sale
    //     foreach ($itemNames as $key => $item_name) {
    //         $item_category = $itemCategories[$key] ?? '';
    //         $quantity = $quantities[$key] ?? 0;

    //         $product = Product::where('product_name', $item_name)
    //             ->where('category', $item_category)
    //             ->first();

    //         if ($product) {
    //             $product->stock -= $quantity;
    //             $product->save();
    //         }
    //     }
    //     return redirect()->route('all-sales')->with('success', 'Sale recorded successfully');
    // }
    public function all_sales()
    {

        if (Auth::id()) {
            $userId = Auth::id();
            $usertype = Auth()->user()->usertype;

            // Retrieve all Sales with their related Purchase data (including invoice_no)
            $Sales = Sale::where('userid', $userId)->where('user_type', $usertype)->get();
            // dd($Sales);
            return view('admin_panel.sale.sales', [
                'Sales' => $Sales,
            ]);
        } else {
            return redirect()->back();
        }
    }

    public function Cash_sales()
    {

        if (Auth::id()) {
            $userId = Auth::id();
            $usertype = Auth()->user()->usertype;

            // Retrieve all Sales with their related Purchase data (including invoice_no)
            $Sales = Sale::where('userid', $userId)
                ->where('user_type', $usertype)
                ->where('sale_type', 'cash')
                ->get();
            // dd($Sales);
            return view('admin_panel.sale.cash_sales', [
                'Sales' => $Sales,
            ]);
        } else {
            return redirect()->back();
        }
    }

    public function get_customer_amount($id)
    {
        // Fetch the customer by customer_id (not id)
        $customer = CustomerCredit::where('customerId', $id)->first();

        // Check if the customer record is found
        if (!$customer) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        // Return the previous amount as JSON
        return response()->json([
            'previous_balance' => $customer->previous_balance // Ensure this field exists in your model
        ]);
    }


    public function downloadInvoice($id)
    {
        $sale = Sale::findOrFail($id);

        // Get customer info if it's a credit sale
        $customer = null;
        $creditInfo = null;

        if ($sale->sale_type === 'credit') {
            $customer = Customer::where('customer_name', $sale->customer)->first();
            $creditInfo = DB::table('customer_credits')
                ->where('customer_name', $sale->customer)
                ->latest()
                ->first();
        }

        return view('admin_panel.invoices.invoice', compact('sale', 'customer', 'creditInfo'));
    }



    // public function downloadInvoice($id)
    // {
    //     // Fetch the sale data
    //     $sale = Sale::findOrFail($id);

    //     // Fetch the customer information based on the customer name in the sale
    //     $customer = Customer::where('customer_name', $sale->customer)->first();

    //     // If customer is not found, handle the case (optional)
    //     if (!$customer) {
    //         abort(404, 'Customer not found for this sale.');
    //     }

    //     // Load the view and pass both sale and customer data
    //     $pdf = Pdf::loadView('admin_panel.invoices.invoice', ['sale' => $sale, 'customer' => $customer]);

    //     // Download the PDF file
    //     return $pdf->download('invoice-' . $sale->invoice_no . '.pdf');
    // }

    public function showReceipt(Request $request, $id)
    {
        // dd($request);
        // Fetch the sale data using the sale ID
        $sale = Sale::findOrFail($id);
        // dd($sale);
        // Get customer credit details
        $customerCredit = CustomerCredit::where('customerId', $sale->customerId)->latest()->first();
        // dd($customerCredit);
        // Initialize variables for previous and closing balance
        $previous_balance = $customerCredit->previous_balance; // Get previous balance from customerCredit
        $closing_balance = $customerCredit->closing_balance;
        // Pass sale data to the receipt view
        return view('admin_panel.sale.receipt', compact('sale', 'customerCredit', 'previous_balance', 'closing_balance'));
    }
}
