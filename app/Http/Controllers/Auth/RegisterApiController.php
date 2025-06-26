<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegisterApiController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                    => ['required', 'string', 'max:255'],
            'email'                   => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'                => ['required', 'string', 'min:8'],
            'mobile'                  => ['nullable', 'string', 'max:255'],
            'addresses'               => ['required', 'array', 'min:1'],
            'addresses.*.address'     => ['required', 'string', 'max:255'],
            'addresses.*.pincode'     => ['nullable', 'string', 'max:10'],
            'addresses.*.landmark'    => ['nullable', 'string', 'max:255'],
            'addresses.*.name'        => ['required', 'string', 'max:255'],
            'addresses.*.instruction' => ['nullable', 'string', 'max:255'],
            'addresses.*.phone'       => ['nullable', 'string', 'max:15'],
            'addresses.*.status'      => ['required', 'in:0,1'],
            'addresses.*.type'        => ['required', 'string', 'in:home,work,other'],
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors()->all()], 422);
        }

        // Create the user with address fields set to null
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'mobile'    => $request->mobile,
            'address1'  => null,
            'address2'  => null,
            'pincode1'  => null,
            'pincode2'  => null,
            'landmark1' => null,
            'landmark2' => null,
        ]);

        // Create addresses in the addresses table
        foreach ($request->addresses as $addressData) {
            $user->addresses()->create([
                'address'     => $addressData['address'],
                'pincode'     => $addressData['pincode'],
                'landmark'    => $addressData['landmark'],
                'name'        => $addressData['name'],
                'instruction' => $addressData['instruction'],
                'phone'       => $addressData['phone'],
                'status'      => $addressData['status'],
                'type'        => $addressData['type'],
            ]);
        }

        // Load the user with addresses for the response
        $user->load('addresses');

        return response(['message' => 'User registered successfully', 'user' => $user], 201);
    }

    public function sendOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required|string',
    ]);

    // ✅ Check if user with this email already exists
    if (User::where('email', $request->email)->exists()) {
        return response()->json([
            'message' => 'Email already registered. Please login instead.'
        ], 409); // HTTP 409 Conflict
    }

    // ✅ Render OTP email using Blade template
    $html = view('email.otp_mail', ['otp' => $request->otp])->render();

    // ✅ Send mail
    Mail::send([], [], function ($message) use ($request, $html) {
        $message->to($request->email)
                ->subject('Your OTP for Myme App')
                ->html($html);
    });

    return response()->json([
        'message' => 'OTP sent successfully to ' . $request->email
    ], 200);
}

}
