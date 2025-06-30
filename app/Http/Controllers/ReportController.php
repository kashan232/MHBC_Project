<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function sale_report()
    {
        if (Auth::id()) {
            $userId = Auth::id();
            // dd($userId);
            return view('admin_panel.reporting.sale-report', []);
        } else {
            return redirect()->back();
        }
    }

    public function filterSales(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $sales = Sale::whereBetween('sale_date', [$start_date, $end_date])
            ->orderBy('sale_date', 'asc')
            ->get();
        return response()->json($sales);
    }
    public function purchase_report()
    {
        if (Auth::id()) {
            $userId = Auth::id();
            // dd($userId);
            return view('admin_panel.reporting.purchase-report', []);
        } else {
            return redirect()->back();
        }
    }
    public function filterpurchase(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $purchase = Purchase::whereBetween('purchase_date', [$start_date, $end_date])
            ->orderBy('purchase_date', 'asc')
            ->get();

        // Check if data is being retrieved
        return response()->json($purchase); // This should return a JSON response
    }

    public function supplier_report()
    {
        if (Auth::id()) {
            $userId = Auth::id();
            $Suppliers = Supplier::get();
            return view('admin_panel.reporting.supplier_purchase_report', [
                'Suppliers' => $Suppliers,
            ]);
        } else {
            return redirect()->back();
        }
    }

    public function filterPurchasesuplir(Request $request)
    {
        $supplier = $request->supplier;
        $start = $request->start_date;
        $end = $request->end_date;

        $query = DB::table('purchases')
            ->where('supplier', $supplier)
            ->whereBetween('purchase_date', [$start, $end])
            ->get();

        return response()->json($query);
    }


    public function PRF_report()
    {
        if (Auth::id()) {
            $userId = Auth::id();
            // dd($userId);
            return view('admin_panel.reporting.PRF-report', []);
        } else {
            return redirect()->back();
        }
    }

    public function PRFfiltersales(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $sales = DB::table('sales')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->get();

        $salesWithProfit = $sales->map(function ($sale) {
            $itemNames = json_decode($sale->item_name, true);
            $quantities = json_decode($sale->quantity, true);
            $prices = json_decode($sale->price, true);

            if (!is_array($itemNames) || !is_array($quantities) || !is_array($prices)) {
                $sale->profit_amounts = [];
                $sale->total_profit = 0;
                return $sale;
            }

            $totalProfit = 0;
            $profitAmounts = [];

            foreach ($itemNames as $index => $productName) {
                $quantity = isset($quantities[$index]) ? (int) $quantities[$index] : 0;
                $retailPrice = isset($prices[$index]) ? (float) $prices[$index] : 0;

                // Check product exists or not
                $product = DB::table('products')
                    ->where('product_name', $productName)
                    ->whereNull('deleted_at')
                    ->first();

                // ❌ Skip this product if not found
                if (!$product) {
                    continue;
                }

                $wholesalePrice = (float) $product->wholesale_price;
                $profitPerUnit = $retailPrice - $wholesalePrice;
                $itemTotalProfit = $profitPerUnit * $quantity;

                $profitAmounts[] = [
                    'product' => $productName,
                    'quantity' => $quantity,
                    'profit_per_unit' => number_format($profitPerUnit, 2),
                    'total_item_profit' => number_format($itemTotalProfit, 2),
                ];

                $totalProfit += $itemTotalProfit;
            }

            $sale->profit_amounts = $profitAmounts;
            $sale->total_profit = $totalProfit;

            return $sale;
        });

        return response()->json($salesWithProfit);
    }
}
