<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserApiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return response()->json($user, 200);
    }

    public function update(Request $request)
    {
        $user = User::where('id', Auth::id())
        ->update([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'pincode1' => $request->pincode1,
            'pincode2' => $request->pincode2,
            'landmark1' => $request->landmark1,
            'landmark2' => $request->landmark2,
        ]);
        return response()->json('success', 200);
    }
}
