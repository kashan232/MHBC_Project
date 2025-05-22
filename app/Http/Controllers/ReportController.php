<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Sale;
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

                $product = DB::table('products')
                    ->where('product_name', $productName)
                    ->whereNull('deleted_at')
                    ->first();

                $wholesalePrice = $product ? (float) $product->wholesale_price : 0;
                $profitPerUnit = $retailPrice - $wholesalePrice;
                $itemTotalProfit = $profitPerUnit * $quantity;

                $profitAmounts[] = number_format($profitPerUnit, 2); // profit per unit for each item
                $totalProfit += $itemTotalProfit;
            }

            $sale->profit_amounts = $profitAmounts; // array of profit per unit
            $sale->total_profit = $totalProfit;     // total profit
            return $sale;
        });


        return response()->json($salesWithProfit);
    }
}
