<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Services\SmsService;

use Illuminate\Http\Request;

class RegisterApiController extends Controller

 {
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required','string','max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if($validator->fails())
        {
            return response(['error' => $validator->errors()->all()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address1' => $request->address1,
            'address2' => $request->address2,
            'pincode1' => $request->pincode1,
            'pincode2' => $request->pincode2,
            'landmark1' => $request->landmark1,
            'landmark2' => $request->landmark2,
        ]);

        return response(['message' => 'User registered successfully', 'user' => $user], 201);
    }



}
