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

class ProductListController extends Controller
{

    /**
     * Show the product list.
     *
     * @return \Illuminate\View\View
     */
    protected function create()
    {
        if(Auth::check()) {
            if(Auth::user()->role=='customer') {
                return view('auth.productList');
            } else {
                return view('auth.productListVendor');
            }
        } else {
            return view('auth.productList');
        }      
    }

}
