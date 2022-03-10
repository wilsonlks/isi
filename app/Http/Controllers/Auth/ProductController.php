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

class ProductController extends Controller
{

    /**
     * Show the form to add a new product.
     *
     * @return \Illuminate\View\View
     */
    protected function create()
    {
        if(Auth::user()->role=='vendor') {
            return view('auth.addProduct');
        } else {
            return redirect('products')->with('alert', 'Sorry, You Are Not Allowed to Access This Page.');
        }        
    }

    /**
     * Store a new product.
     *
     * @return \Illuminate\View\View
     */
    protected function store()
    {
        if(Auth::user()->role=='vendor') {
            return view('auth.addProduct');
        } else {
            return redirect('products')->with('alert', 'Sorry, You Are Not Allowed to Access This Page.');
        }        
    }

    /**
     * Edit a product.
     *
     * @return \Illuminate\View\View
     */
    protected function edit()
    {
        if(Auth::user()->role=='vendor') {
            return view('auth.editProduct');
        } else {
            return redirect('products')->with('alert', 'Sorry, You Are Not Allowed to Access This Page.');
        }        
    }

    /**
     * Show the best selling products.
     *
     * @return \Illuminate\View\View
     */
    protected function best_selling()
    {
        if(Auth::user()->role=='vendor') {
            return view('auth.bestSellingProducts');
        } else {
            return redirect('products')->with('alert', 'Sorry, You Are Not Allowed to Access This Page.');
        }        
    }

}
