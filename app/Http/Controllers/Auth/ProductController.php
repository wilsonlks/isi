<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
// use App\Models\User;
// use Illuminate\Foundation\Auth\RegistersUsers;
// use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
        return view('auth.addProduct');
    }

    // /**
    //  * Store a new product.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function store(Request $request)
    // {
    //     // Validate and store the product

    //     $validated = $request->validate([
    //         'product-name' => ['required'],
    //         'category' => ['required'],
    //         'price' => ['required', 'numeric', 'between:0, 99999.99'],
    //         'stock' => ['required', 'numeric', 'integer', 'min:0'],
    //         'description1' => ['required'],
    //         'description2' => ['required'],
    //         'file' => ['required'],
    //     ]);
    // }

}
