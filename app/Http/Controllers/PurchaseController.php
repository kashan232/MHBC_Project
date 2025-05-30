<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ClaimReturn;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\StaffSalary;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function Purchase()
    {
        if (Auth::id()) {
            $userId = Auth::id();
            // dd($userId);
            $Purchases = Purchase::get();
            // dd($Purchases);
            return view('admin_panel.purchase.purchase', [
                'Purchases' => $Purchases,
            ]);
        } else {
            return redirect()->back();
        }
    }

    public function add_purchase()
    {
        if (Auth::id()) {
            $userId = Auth::id();
            // dd($userId);
            $Suppliers = Supplier::get();
            $Warehouses = Warehouse::get();
            $Category = Category::get();
            return view('admin_panel.purchase.add_purchase', [
                'Suppliers' => $Suppliers,
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

    public function store_Purchase(Request $request)
    {
        $userId = Auth::id();

        // dd($request);
        // Validate the request
        $validatedData = $request->validate([
            'supplier' => 'required|string',
            'purchase_date' => 'required|date',
            'warehouse_id' => 'required|string',
            'item_category' => 'required|array',
            'item_name' => 'required|array',
            'quantity' => 'required|array',
            'price' => 'required|array',  // This is an array of prices
            'total' => 'required|array',
            'note' => 'nullable|string',
            'total_price' => 'required|numeric',
            'discount' => 'nullable|numeric',  // Ensure it's numeric
        ]);

        $invoiceNo = Purchase::generateInvoiceNo();

        // Ensure discount is numeric and default to 0 if null
        $discount = (float) $request->discount ?? 0;

        // Ensure total_price is numeric as well
        $totalPrice = (float) $request->total_price;

        // Prepare data for storage
        $purchaseData = [
            'invoice_no' => $invoiceNo,
            'supplier' => $request->supplier,
            'purchase_date' => $request->purchase_date,
            'warehouse_id' => $request->warehouse_id,
            'item_category' => json_encode($request->item_category),
            'item_name' => json_encode($request->item_name),
            'quantity' => json_encode($request->quantity),
            'price' => json_encode($request->price),  // Array of prices
            'total' => json_encode($request->total),
            'note' => $request->note,
            'total_price' => $totalPrice,
            'discount' => $discount,
            'Payable_amount' => $totalPrice - $discount, // Correct subtraction with numeric values
            'paid_amount' => $request->paid_amount,
            'due_amount' => $request->due_amount,

        ];

        // Save purchase data
        $purchase = Purchase::create($purchaseData);

        // Step 2: Update Product Stock and Wholesale Price
        foreach ($request->item_name as $key => $item_name) {
            $item_category = $request->item_category[$key];
            $quantity = $request->quantity[$key];
            $purchase_price = $request->price[$key];  // Single price for the item

            // Find the product and update stock and wholesale price
            $product = Product::where('product_name', $item_name)
                ->where('category', $item_category)
                ->first();

            if ($product) {
                $product->stock += $quantity; // Increase the stock
                $product->wholesale_price = $purchase_price;  // Set wholesale price to purchase price
                $product->save();
            }
        }

        $previousBalance = SupplierLedger::where('supplier_id', $request->supplier)
            ->value('closing_balance') ?? 0; // If no previous balance, start from 0
        // Calculate new balances

        $newPreviousBalance = $totalPrice - $discount;

        $newClosingBalance = $previousBalance + $newPreviousBalance;

        // Update or create distributor ledger
        SupplierLedger::updateOrCreate(
            ['supplier_id' => $request->supplier],
            [
                'supplier_id' => $request->supplier,
                'admin_or_user_id' => $userId,
                'previous_balance' => $newPreviousBalance,
                'closing_balance' => $newClosingBalance,
            ]
        );

        return redirect()->back()->with('success', 'Purchase saved successfully and stock updated.');
    }



    // public function store_Purchase(Request $request)
    // {
    //     // Validate the request
    //     $validatedData = $request->validate([
    //         'invoice_no' => 'required|string',
    //         'supplier' => 'required|string',
    //         'purchase_date' => 'required|date',
    //         'warehouse_id' => 'required|string',
    //         'item_category' => 'required|array',
    //         'item_name' => 'required|array',
    //         'quantity' => 'required|array',
    //         'price' => 'required|array',
    //         'total' => 'required|array',
    //         'note' => 'nullable|string',
    //         'total_price' => 'required|numeric',
    //         'discount' => 'nullable|numeric',  // Ensure it's numeric
    //     ]);

    //     // Ensure discount is numeric and default to 0 if null
    //     $discount = (float) $request->discount ?? 0;

    //     // Ensure total_price is numeric as well
    //     $totalPrice = (float) $request->total_price;

    //     // Prepare data for storage
    //     $purchaseData = [
    //         'invoice_no' => $request->invoice_no,
    //         'supplier' => $request->supplier,
    //         'purchase_date' => $request->purchase_date,
    //         'warehouse_id' => $request->warehouse_id,
    //         'item_category' => json_encode($request->item_category),
    //         'item_name' => json_encode($request->item_name),
    //         'quantity' => json_encode($request->quantity),
    //         'price' => json_encode($request->price),
    //         'total' => json_encode($request->total),
    //         'note' => $request->note,
    //         'total_price' => $totalPrice,
    //         'discount' => $discount,
    //         'Payable_amount' => $totalPrice - $discount, // Correct subtraction with numeric values
    //     ];

    //     // Save purchase data
    //     $purchase = Purchase::create($purchaseData);

    //     // Step 2: Update Product Stock
    //     foreach ($request->item_name as $key => $item_name) {
    //         $item_category = $request->item_category[$key];
    //         $quantity = $request->quantity[$key];

    //         // Find the product and update stock
    //         $product = Product::where('product_name', $item_name)
    //             ->where('category', $item_category)
    //             ->first();
    //         if ($product) {
    //             $product->stock += $quantity; // Increase the stock
    //             $product->wholesale_price = $purchase->price;
    //             $product->save();
    //         }
    //     }

    //     return redirect()->back()->with('success', 'Purchase saved successfully and stock updated.');
    // }



    public function purchases_payment(Request $request)
    {
        // Retrieve the purchase record
        $purchase = Purchase::find($request->purchase_id);

        if ($purchase) {
            // Update the paid amount and calculate the remaining amount
            $paidAmount = $request->paid_amount;
            $newPaidAmount = $purchase->paid_amount + $paidAmount;
            $remainingAmount = $purchase->Payable_amount - $newPaidAmount;

            // Update the purchase record
            $purchase->paid_amount = $newPaidAmount; // Assuming you have a 'paid_amount' column in your purchase table
            $purchase->due_amount = $remainingAmount > 0 ? $remainingAmount : 0; // Calculate due amount
            $purchase->Payable_amount = $remainingAmount <= 0 ? 0 : $remainingAmount; // Update payable amount

            // Update the status based on whether the payment is complete or not
            if ($remainingAmount <= 0) {
                $purchase->status = 'Paid'; // Assuming you have a 'status' column in your purchase table
            } else {
                $purchase->status = 'Unpaid';
            }

            $purchase->save();

            return redirect()->back()->with('success', 'Payment made successfully. Status updated.');
        }

        return redirect()->back()->with('error', 'Purchase record not found.');
    }

    public function purchase_return($id)
    {
        if (Auth::id()) {
            $userId = Auth::id();

            // Fetch suppliers, warehouses, and categories
            $Suppliers = Supplier::get();
            $Warehouses = Warehouse::get();
            $Category = Category::get();

            // Fetch the specific purchase by ID and make sure we get the item details
            $purchase = Purchase::where('id', $id)->first();

            if ($purchase) {
                // Decode JSON fields from the purchase table for items, quantities, and prices
                $itemNames = json_decode($purchase->item_name, true);
                $quantities = json_decode($purchase->quantity, true);
                $prices = json_decode($purchase->price, true);
                $totals = json_decode($purchase->total, true);

                // Initialize an array to store stock quantities
                $stocks = [];

                // Loop through item names and fetch stock quantity from the Product table
                foreach ($itemNames as $itemName) {
                    // Fetch product based on the product_name
                    $product = Product::where('product_name', $itemName)->first();

                    if ($product) {
                        // Store stock quantity if the product is found
                        $stocks[] = $product->stock;
                    } else {
                        // If product not found, store 0 stock
                        $stocks[] = 0;
                    }
                }

                // dd($stocks); // Debugging line to check stock values
                // Pass all data including stocks to the blade
                return view('admin_panel.purchase_return.add_purchase_return', [
                    'Suppliers' => $Suppliers,
                    'Warehouses' => $Warehouses,
                    'Category' => $Category,
                    'purchase' => $purchase,
                    'itemNames' => $itemNames,
                    'quantities' => $quantities,
                    'prices' => $prices,
                    'totals' => $totals,
                    'stocks' => $stocks // Pass the stock quantities to the view
                ]);
            } else {
                return redirect()->back()->with('error', 'Purchase not found');
            }
        } else {
            return redirect()->back();
        }
    }
    public function store_purchase_return(Request $request)
    {
        // Retrieve inputs from the request
        $itemNames = $request->input('item_name', []);
        $returnQuantities = array_map('floatval', $request->input('return_qty', [])); // Convert quantities to float
        $unitPrices = array_map('floatval', $request->input('price', [])); // Convert unit prices to float
        $purchaseId = $request->input('purchase_id');

        // Ensure item names, return quantities, and unit prices are arrays
        if (!is_array($itemNames) || !is_array($returnQuantities) || !is_array($unitPrices)) {
            return redirect()->back()->with('error', 'Invalid data provided.');
        }

        // Update stock quantities in the Product table
        foreach ($itemNames as $index => $itemName) {
            $returnQty = $returnQuantities[$index] ?? 0;
            $product = Product::where('product_name', $itemName)->first();

            if ($product) {
                // Subtract the return quantity from the current stock
                $product->stock -= $returnQty;
                $product->save();
            }
        }

        // Fetch the purchase record to update quantities
        $purchase = Purchase::where('id', $purchaseId)->first();
        if ($purchase) {
            $itemNamesInPurchase = json_decode($purchase->item_name, true);
            $quantitiesInPurchase = json_decode($purchase->quantity, true);

            // Update quantities in the purchase record
            foreach ($itemNames as $index => $itemName) {
                $returnQty = $returnQuantities[$index] ?? 0;
                $itemIndex = array_search($itemName, $itemNamesInPurchase);

                if ($itemIndex !== false) {
                    // Subtract the return quantity from the existing quantity
                    $quantitiesInPurchase[$itemIndex] -= $returnQty;

                    // Ensure quantity does not go below zero
                    if ($quantitiesInPurchase[$itemIndex] < 0) {
                        $quantitiesInPurchase[$itemIndex] = 0;
                    }
                }
            }

            // Save updated quantities and item names as strings
            $purchase->quantity = json_encode(array_map('strval', $quantitiesInPurchase));
            $purchase->item_name = json_encode($itemNamesInPurchase);
            $purchase->save();
        }

        // Update purchase record to set is_return flag
        Purchase::where('id', $purchaseId)->update(['is_return' => 1]);

        // Calculate totals
        $totals = [];
        $subtotal = 0;
        foreach ($returnQuantities as $index => $quantity) {
            $price = $unitPrices[$index] ?? 0;
            $total = $quantity * $price;
            $totals[] = strval($total);  // Convert total to string
            $subtotal += $total;
        }

        // Prepare data for storage and ensure all numeric arrays are saved as strings
        $purchaseReturnData = [
            'purchase_id' => $purchaseId,
            'return_date' => $request->input('return_date'),
            'supplier' => $request->input('supplier'),
            'warehouse_id' => $request->input('warehouse'),
            'item_name' => json_encode($itemNames),
            'return_quantity' => json_encode(array_map('strval', $returnQuantities)),  // Convert quantities to strings
            'price' => json_encode(array_map('strval', $unitPrices)),  // Convert prices to strings
            'total' => json_encode($totals),  // Totals are already converted to strings above
            'note' => $request->input('note'),
            'discount' => $request->input('discount') ?? 0,
            'subtotal' => strval($subtotal),  // Subtotal as string
            'total_price' => strval($subtotal),  // Assuming total_price should be the subtotal
            'payable_amount' => strval($request->input('payable_amount'))
        ];

        // Save purchase return to the database
        PurchaseReturn::create($purchaseReturnData);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Purchase return saved, stock updated, and purchase marked as returned successfully.');
    }

    public function all_purchase_return()
    {
        if (Auth::id()) {
            $userId = Auth::id();

            // Retrieve all PurchaseReturns with their related Purchase data (including invoice_no)
            $PurchaseReturns = PurchaseReturn::with('purchase')->get();
            // dd($PurchaseReturns);
            return view('admin_panel.purchase_return.purchase_return', [
                'PurchaseReturns' => $PurchaseReturns,
            ]);
        } else {
            return redirect()->back();
        }
    }

    public function purchase_return_payment(Request $request)
    {
        $purchaseReturn = PurchaseReturn::find($request->purchase_id);

        if ($purchaseReturn) {
            // Calculate the new paid and due amounts
            $newPaidAmount = $purchaseReturn->paid_amount + $request->paid_amount;
            $dueAmount = $purchaseReturn->payable_amount - $newPaidAmount;

            // Update the PurchaseReturn record
            $purchaseReturn->paid_amount = $newPaidAmount;
            $purchaseReturn->due_amount = $dueAmount;

            // Update the status based on payment completion
            $purchaseReturn->status = $dueAmount <= 0 ? 'Paid' : 'Due';

            $purchaseReturn->save();

            return redirect()->back()->with('success', 'Payment made successfully.');
        } else {
            return redirect()->back()->with('error', 'Purchase Return not found.');
        }
    }

    public function getUnitByProduct($productId)
    {
        $product = Product::where('product_name', $productId)->first();
        return response()->json([
            'unit' => $product->unit,
        ]);
    }


    public function purchase_return_damage_item($id)
    {
        if (Auth::id()) {
            $userId = Auth::id();

            // Fetch suppliers, warehouses, and categories
            $Suppliers = Supplier::get();
            $Warehouses = Warehouse::get();
            $Category = Category::get();

            // Fetch the specific purchase by ID and make sure we get the item details
            $purchase = Purchase::where('id', $id)->first();

            if ($purchase) {
                // Decode JSON fields from the purchase table for items, quantities, and prices
                $itemNames = json_decode($purchase->item_name, true);
                $quantities = json_decode($purchase->quantity, true);
                $prices = json_decode($purchase->price, true);
                $totals = json_decode($purchase->total, true);

                // Initialize an array to store stock quantities
                $stocks = [];

                // Loop through item names and fetch stock quantity from the Product table
                foreach ($itemNames as $itemName) {
                    // Fetch product based on the product_name
                    $product = Product::where('product_name', $itemName)->first();

                    if ($product) {
                        // Store stock quantity if the product is found
                        $stocks[] = $product->stock;
                    } else {
                        // If product not found, store 0 stock
                        $stocks[] = 0;
                    }
                }

                // dd($stocks); // Debugging line to check stock values
                // Pass all data including stocks to the blade
                return view('admin_panel.claim_return.add_claim_return', [
                    'Suppliers' => $Suppliers,
                    'Warehouses' => $Warehouses,
                    'Category' => $Category,
                    'purchase' => $purchase,
                    'itemNames' => $itemNames,
                    'quantities' => $quantities,
                    'prices' => $prices,
                    'totals' => $totals,
                    'stocks' => $stocks // Pass the stock quantities to the view
                ]);
            } else {
                return redirect()->back()->with('error', 'Purchase not found');
            }
        } else {
            return redirect()->back();
        }
    }

    public function store_purchase_return_damage_item(Request $request)
    {
        // Retrieve inputs from the request
        $itemNames = $request->input('item_name', []);
        $returnQuantities = array_map('floatval', $request->input('return_qty', [])); // Convert quantities to float
        $unitPrices = array_map('floatval', $request->input('price', [])); // Convert unit prices to float
        $purchaseId = $request->input('purchase_id');

        // Ensure item names, return quantities, and unit prices are arrays
        if (!is_array($itemNames) || !is_array($returnQuantities) || !is_array($unitPrices)) {
            return redirect()->back()->with('error', 'Invalid data provided.');
        }

        // Update stock quantities in the Product table
        foreach ($itemNames as $index => $itemName) {
            $returnQty = $returnQuantities[$index] ?? 0;
            $product = Product::where('product_name', $itemName)->first();

            if ($product) {
                // Subtract the return quantity from the current stock
                $product->stock -= $returnQty;
                $product->save();
            }
        }

        // Fetch the purchase record to update quantities
        $purchase = Purchase::where('id', $purchaseId)->first();
        if ($purchase) {
            $itemNamesInPurchase = json_decode($purchase->item_name, true);
            $quantitiesInPurchase = json_decode($purchase->quantity, true);

            // Update quantities in the purchase record
            foreach ($itemNames as $index => $itemName) {
                $returnQty = $returnQuantities[$index] ?? 0;
                $itemIndex = array_search($itemName, $itemNamesInPurchase);

                if ($itemIndex !== false) {
                    // Subtract the return quantity from the existing quantity
                    $quantitiesInPurchase[$itemIndex] -= $returnQty;

                    // Ensure quantity does not go below zero
                    if ($quantitiesInPurchase[$itemIndex] < 0) {
                        $quantitiesInPurchase[$itemIndex] = 0;
                    }
                }
            }

            // Save updated quantities and item names as strings
            $purchase->quantity = json_encode(array_map('strval', $quantitiesInPurchase));
            $purchase->item_name = json_encode($itemNamesInPurchase);
            $purchase->save();
        }

        // Update purchase record to set is_return flag
        Purchase::where('id', $purchaseId)->update(['is_return' => 1]);

        // Calculate totals
        $totals = [];
        $subtotal = 0;
        foreach ($returnQuantities as $index => $quantity) {
            $price = $unitPrices[$index] ?? 0;
            $total = $quantity * $price;
            $totals[] = strval($total);  // Convert total to string
            $subtotal += $total;
        }

        // Prepare data for storage and ensure all numeric arrays are saved as strings
        $ClaimReturn = [
            'purchase_id' => $purchaseId,
            'return_date' => $request->input('return_date'),
            'supplier' => $request->input('supplier'),
            'warehouse_id' => $request->input('warehouse'),
            'item_name' => json_encode($itemNames),
            'return_quantity' => json_encode(array_map('strval', $returnQuantities)),  // Convert quantities to strings
            'price' => json_encode(array_map('strval', $unitPrices)),  // Convert prices to strings
            'total' => json_encode($totals),  // Totals are already converted to strings above
            'note' => $request->input('note'),
            'discount' => $request->input('discount') ?? 0,
            'subtotal' => strval($subtotal),  // Subtotal as string
            'total_price' => strval($subtotal),  // Assuming total_price should be the subtotal
            'payable_amount' => strval($request->input('payable_amount'))
        ];

        // Save purchase return to the database
        ClaimReturn::create($ClaimReturn);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Purchase return saved, stock updated, and purchase marked as returned successfully.');
    }
    public function all_purchase_return_damage_item()
    {
        if (Auth::id()) {
            $userId = Auth::id();

            // Retrieve all ClaimReturn with their related Purchase data (including invoice_no)
            $ClaimReturns = ClaimReturn::with('purchase')->get();
            // dd($ClaimReturn);
            return view('admin_panel.claim_return.claim_return', [
                'ClaimReturns' => $ClaimReturns,
            ]);
        } else {
            return redirect()->back();
        }
    }
}
