<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAddressRequest;
use App\Models\UserAddress;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    public function index(Request $request)
    {
        $address = $request->user()->address;
        return view('user_address.index', compact('address'));
    }

    public function create(UserAddress $address)
    {
        return view('user_address.create_and_edit', compact('address'));
    }

    public function store(UserAddressRequest $request)
    {
        $request->user()->address()->create($request->only([
            'province',
            'city',
            'district',
            'address',
            'zip',
            'contact_name',
            'contact_phone',
        ]));

        return redirect()->route('address.index');
    }
    public function edit(UserAddress $address)
    {
        return view('user_address.create_and_edit', compact('address'));
    }

    public function update(UserAddress $address, UserAddressRequest $request)
    {
        $address->update($request->only([
            'province',
            'city',
            'district',
            'address',
            'zip',
            'contact_name',
            'contact_phone',
        ]));
        return redirect()->route('address.index');
    }

    public function destroy(UserAddress $address)
    {
        $address->delete();
        return redirect()->route('address.index');
    }
}
