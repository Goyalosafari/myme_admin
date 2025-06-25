<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserApiController extends Controller
{
    public function index()
    {
        $user = Auth::User();
        return response()->json(['user' => $user], 200);
    }

    public function userInfo(Request $request)
    {
        $user = User::with('addresses')->where('id', $request->user_id)->first();

        if (! $user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json(['user' => $user], 200);
    }

    /// $requestData['user_id'];
    //where('user_id', $request->user_id)
    //$user = User::where('id', Auth::id())
    public function update(Request $request)
    {
        $user = User::findOrFail($request->user_id);

        // Step 1: Update user fields
        $user->update([
            'name'   => $request->name,
            'mobile' => $request->mobile,
        ]);

        // Step 2: Update addresses if present
        if ($request->has('addresses')) {
            foreach ($request->addresses as $addr) {
                if (isset($addr['id'])) {
                    // Update existing address by ID
                    $user->addresses()->where('id', $addr['id'])->update([
                        'address'  => $addr['address'],
                        'pincode'  => $addr['pincode'],
                        'landmark' => $addr['landmark'],
                        'status'   => $addr['status'],
                        'type'     => $addr['type'],
                    ]);
                } else {
                    // Optionally create new address if no ID is provided
                    $user->addresses()->create([
                        'address'  => $addr['address'],
                        'pincode'  => $addr['pincode'],
                        'landmark' => $addr['landmark'],
                        'status'   => $addr['status'],
                        'type'     => $addr['type'],
                    ]);
                }
            }
        }

        return response()->json(['message' => 'User updated successfully'], 200);
    }

    public function newPassword(Request $request)
    {
        $user = User::where('mobile', $request->mobile)
            //$user = User::where('id', Auth::id())
            ->update([
                'password' => Hash::make($request->password),
            ]);
        return response()->json(['status' => 'success'], 200);
    }

    public function deactivateUser(Request $request)
    {
        $user         = User::where('id', $request->user_id)->first();
        $user->email  = 'deactivated-' . $user->email;
        $user->mobile = '0000-' . $user->mobile;
        $user->status = 2;
        $user->save();

        return response()->json(['status' => 'success'], 200);
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
            $user->email  = 'deactivated-' . $user->email;
            $user->mobile = '0000-' . $user->mobile;
            $user->status = 2;
            $user->save();

            return response()->json(['status' => 'success', 'message' => 'User deactivated successfully'], 200);
        }

        // If authentication fails
        return response()->json(['status' => 'error', 'message' => 'Invalid email or password'], 401);
    }
}
