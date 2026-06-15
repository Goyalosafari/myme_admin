<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserApiController extends Controller
{
    public function index()
    {
        $user = Auth::User();
        return response()->json(['user'=>$user], 200);
    }
    
        public function userInfo(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Build addresses array from flat DB fields
        $addresses = [];

        if (!empty($user->address1)) {
            $addresses[] = [
                'type'     => 'home',
                'address'  => $user->address1,
                'pincode'  => $user->pincode1  ?? '',
                'landmark' => $user->landmark1 ?? '',
                'phone'    => $user->mobile    ?? '',
            ];
        }

        if (!empty($user->address2)) {
            $addresses[] = [
                'type'     => 'work',
                'address'  => $user->address2,
                'pincode'  => $user->pincode2  ?? '',
                'landmark' => $user->landmark2 ?? '',
                'phone'    => $user->mobile    ?? '',
            ];
        }

        $userData               = $user->toArray();
        $userData['addresses']  = $addresses;

        return response()->json(['user' => $userData], 200);
    }

/// $requestData['user_id'];
//where('user_id', $request->user_id)
//$user = User::where('id', Auth::id())

    public function update(Request $request)
    
    {
        $user = User::where('id', $request->user_id)
       //$user = User::where('id', Auth::id())
        ->update([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'pincode1' => $request->pincode1,
            'pincode2' => $request->pincode2,
            'landmark1' => $request->landmark1,
            'landmark2' => $request->landmark2,
            'active_addr' => $request->active,
        ]);
        return response()->json('success', 200);
    }
    
        public function newPassword(Request $request)
    
    {
        $user = User::where('mobile', $request->mobile)
       //$user = User::where('id', Auth::id())
        ->update([
            'password' => Hash::make($request->password),
        ]);
        return response()->json(['status' => 'success'],200);
    }
    
    
    public function applyRewardPoints(Request $request)
    {
        // Flutter handles wallet deduction locally; this endpoint just acknowledges the preference.
        return response()->json(['status' => 'success', 'use_points' => $request->use_points], 200);
    }

    public function deactivateUser(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        $user->email = 'deactivated-' .  $user->email;
        $user->mobile = '0000-' . $user->mobile;
        $user->status = 2;
        $user->save();
        
        return response()->json(['status' => 'success'],200);
    }

public function deactivatewebUser(Request $request)
{
    // Validate email and password
    $credentials = $request->only('email', 'password');

    // Attempt to authenticate the user
    if (Auth::attempt($credentials)) {
        // Retrieve the authenticated user
        $user = Auth::user();

        // Perform the deactivation
        $user->email = 'deactivated-' . $user->email;
        $user->mobile = '0000-' . $user->mobile;
        $user->status = 2;
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'User deactivated successfully'], 200);
    }

    // If authentication fails
    return response()->json(['status' => 'error', 'message' => 'Invalid email or password'], 401);
}

}
