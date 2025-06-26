<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


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
        // Validate request data
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user_id],
            'password' => ['sometimes', 'string', 'min:8'],
            'mobile' => ['nullable', 'string', 'max:255'],
            'addresses' => ['sometimes', 'array', 'min:1'],
            'addresses.*.id' => ['sometimes', 'exists:addresses,id'],
            'addresses.*.address' => ['required', 'string', 'max:255'],
            'addresses.*.pincode' => ['nullable', 'string', 'max:10'],
            'addresses.*.landmark' => ['nullable', 'string', 'max:255'],
            'addresses.*.name' => ['required', 'string', 'max:255'],
            'addresses.*.instruction' => ['nullable', 'string', 'max:255'],
            'addresses.*.phone' => ['nullable', 'string', 'max:15'],
            'addresses.*.status' => ['required', 'in:0,1'],
            'addresses.*.type' => ['required', 'string', 'in:home,work,other'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->messages()], 422);
        }

        // Find the user
        $user = User::findOrFail($request->user_id);

        // Update user fields
        $user->update(array_filter([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : null,
            'mobile' => $request->mobile,
        ], fn($value) => !is_null($value)));

        // Update or create addresses if provided
        if ($request->has('addresses')) {
            foreach ($request->addresses as $addr) {
                $addressData = [
                    'address' => $addr['address'],
                    'pincode' => $addr['pincode'] ?? null,
                    'landmark' => $addr['landmark'] ?? null,
                    'name' => $addr['name'],
                    'instruction' => $addr['instruction'] ?? null,
                    'phone' => $addr['phone'] ?? null,
                    'status' => $addr['status'],
                    'type' => $addr['type'],
                ];

                if (isset($addr['id'])) {
                    // Update existing address
                    $user->addresses()->where('id', $addr['id'])->update($addressData);
                } else {
                    // Create new address
                    $user->addresses()->create($addressData);
                }
            }
        }

        // Load the user with addresses for the response
        $user->load('addresses');

        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
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
