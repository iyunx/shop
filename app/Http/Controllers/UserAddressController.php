<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    public function index(Request $request)
    {
        $address = $request->user()->address;
        return view('user_address.index', compact('address'));
    }
}
