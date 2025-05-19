<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
