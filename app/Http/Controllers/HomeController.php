<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;


class HomeController extends Controller
{
    public function home()
    {
        if (Auth::id()) {
            $usertype = Auth()->user()->usertype;

            if ($usertype == 'staff') {

                // Fetch all categories for the dropdown
                $categories = Category::all();

                // Initially, load all products for display (optional, can be removed if you prefer to only load products on category change)
                $products = Product::all();
                $Customers = Customer::all();
                $Warehouses = Warehouse::get();


                return view('user_panel.user_dashboard', compact('categories', 'products', 'Customers','Warehouses'));
            } else if ($usertype == 'admin') {
                $userId = Auth::id();
                $totalPurchasesPrice = \App\Models\Purchase::sum('total_price');
                $totalPurchaseReturnsPrice = \App\Models\PurchaseReturn::sum('total_price');
                // Fetch all products for the logged-in admin
                $all_product = Product::where('admin_or_user_id', '=', $userId)->get();

                // Calculate total stock value for all products
                $totalStockValue = $all_product->sum(function ($product) {
                    return $product->stock * $product->wholesale_price;
                });


                // Calculate total stock value for each product
                foreach ($all_product as $product) {
                    $product->total_stock_value = $product->stock * $product->wholesale_price;
                }


                $categories = DB::table('categories')->count();
                $products = DB::table('products')->count();
                $suppliers = DB::table('suppliers')->count();
                $customers = DB::table('customers')->count();
                $totalsales = DB::table('sales')->sum('Payable_amount');

                // $lowStockProducts = Product::whereRaw('CAST(stock AS UNSIGNED) <= CAST(alert_quantity AS UNSIGNED)')->get();
                // dd($lowStockProducts);
                return view('admin_panel.admin_dashboard', compact('totalPurchasesPrice', 'totalPurchaseReturnsPrice', 'all_product', 'totalStockValue', 'categories', 'products', 'suppliers', 'customers','totalsales'));
            }
        } else {
            return Redirect()->route('login');
        }
    }

    public function adminpage()
    {
        return view('admin_panel.admin_page');
    }

    public function dashboard()
    {
        return view('admin_panel.dashboard');
    }

    public function Admin_Change_Password()
    {
        if (Auth::id()) {
            $userId = Auth::id();
            return view('admin_panel.change_password', []);
        } else {
            return redirect()->back();
        }
    }

    public function updte_change_Password(Request $request)
    {
        if (Auth::id()) {
            // dd($request);
            // Validate the form data
            $request->validate([
                'old_password' => 'required',
                'new_password' => 'required|min:8',
                'retype_new_password' => 'required|same:new_password'
            ]);

            // Get the current authenticated user
            $user = Auth::user();
            // dd($user);
            // Check if the old password matches
            if (!Hash::check($request->input('old_password'), $user->password)) {
                return redirect()->back()->withErrors(['old_password' => 'Old password is incorrect']);
            }

            // Check if the user is an admin
            if ($user->usertype !== 'admin') {
                return redirect()->back()->withErrors(['error' => 'Unauthorized action']);
            }

            // Update the password
            $user->password = Hash::make($request->input('new_password'));
            $user->save();

            // Redirect back with success message
            return redirect()->back()->with('success', 'Password changed successfully');

            // Redirect back with success message
            return redirect()->back();
        } else {
            return redirect()->back();
        }
    }

    // Staff work 

    public function getProductsByCategory(Request $request)
    {
        $categoryname = $request->categoryname;
        // dd($categoryname);
        // Fetch products based on the selected category
        $products = Product::where('category', $categoryname)->get();
        // dd($products);
        // Return JSON response
        return response()->json($products);
    }

    public function getProductByBarcode(Request $request)
    {
        $barcode = $request->query('barcode'); // Get barcode from query parameters
        $product = Product::where('barcode_number', $barcode)->first();

        if ($product) {
            return response()->json($product);
        } else {
            return response()->json(null, 404);
        }
    }
}
