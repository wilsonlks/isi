<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class OrderController extends Controller
{

    /**
     * Show the purchase orders.
     *
     * @return \Illuminate\View\View
     */
    protected function get_orders()
    {
        if(Auth::check()) {
            if(Auth::user()->role=='customer') {
                return view('auth.purchaseTrackingPage');
            } else {
                return view('auth.purchaseOrderListPage');
            }
        } else {
            return redirect('products')->with('alert', 'Sorry, You Are Not Allowed to Access This Page.');
        }
    }

    /**
     * Show a specific purchase order.
     *
     * @return \Illuminate\View\View
     */
    protected function get_order_detail()
    {
        if(Auth::check()) {
            if(Auth::user()->role=='customer') {
                return view('auth.purchaseOrderDetailPage');
            } else {
                return view('auth.purchaseOrderProcessingPage');
            }
        } else {
            return redirect('products')->with('alert', 'Sorry, You Are Not Allowed to Access This Page.');
        }
    }

    /**
     * Process a specific purchase order.
     *
     * @return \Illuminate\View\View
     */
    protected function process_order_detail()
    {
        if(Auth::check()) {
            if(Auth::user()->role=='customer') {
                return view('auth.purchaseOrderDetailPage');
            } else {
                return view('auth.purchaseOrderProcessingPage');
            }
        } else {
            return redirect('products')->with('alert', 'Sorry, You Are Not Allowed to Access This Page.');
        }
    }

    // /**
    //  * Rate and review a specific purchase order.
    //  *
    //  * @return \Illuminate\View\View
    //  */
    // protected function rate_and_review()
    // {
    //     if(Auth::user()->role=='customer') {
    //         return view('auth.rateAndReview');
    //     } else {
    //         return redirect('orders')->with('alert', 'Sorry, You Are Not Allowed to Access This Page.');
    //     }        
    // }

}
