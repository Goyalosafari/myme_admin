<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressApiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id'     => 'required|integer|exists:users,id',
            'address'     => 'required|string|max:255',
            'pincode'     => 'nullable|string|max:10',
            'landmark'    => 'nullable|string|max:255',
            'name'        => 'required|string|max:255',
            'instruction' => 'nullable|string|max:255',
            'phone'       => 'nullable|string|max:15',
            'status'      => 'required|in:0,1',
            'type'        => 'required|string|in:home,work,other',
        ]);
        $user = \App\Models\User::findOrFail($request->user_id);
        $address = $user->addresses()->create($request->except('user_id'));
        return response(['message' => 'Address added successfully', 'address' => $address], 201);
    }
}
