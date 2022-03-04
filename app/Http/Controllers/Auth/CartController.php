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

class CartController extends Controller
{

    /**
     * Show the shopping cart of a customer.
     *
     * @return \Illuminate\View\View
     */
    protected function get_shopping_cart()
    {
        if(Auth::user()->role=='customer') {
            return view('auth.shoppingCart');
        } else {
            return redirect('products')->with('alert', 'Sorry, You Are Not Allowed to Access This Page.');
        }        
    }

    /**
     * Check out all items in shopping cart.
     *
     * @return \Illuminate\View\View
     */
    protected function purchase()
    {
        if(Auth::user()->role=='customer') {
            return view('auth.shoppingCart');
        } else {
            return redirect('products')->with('alert', 'Sorry, You Are Not Allowed to Access This Page.');
        }        
    }

}
