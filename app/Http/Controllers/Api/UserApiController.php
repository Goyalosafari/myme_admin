<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Address;
use App\Models\LoyaltySetting;
use App\Models\LoyaltyTransaction;
use App\Models\ReferralSetting;
use App\Models\User;
use App\Models\Wallet;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserApiController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return $this->error('Unauthenticated', 401);
        }

        if ($user->isDeactivated()) {
            return $this->error('This account has been deactivated. Please contact support.', 403);
        }

        $resource = (new UserResource($user))->resolve();
        return response()->json(['success' => true, 'data' => $resource, 'user' => $resource]);
    }

    public function userInfo(Request $request)
    {
        $user = User::find($request->user_id);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        if ($user->isDeactivated()) {
            return $this->error('This account has been deactivated. Please contact support.', 403);
        }

        $resource = (new UserResource($user))->resolve();
        return response()->json(['success' => true, 'data' => $resource, 'user' => $resource]);
    }

    public function update(Request $request)
    {
        User::where('id', $request->user_id)->update(array_filter([
            'name'        => $request->name,
            'mobile'      => $request->mobile,
            'address1'    => $request->address1,
            'address2'    => $request->address2,
            'pincode1'    => $request->pincode1,
            'pincode2'    => $request->pincode2,
            'landmark1'   => $request->landmark1,
            'landmark2'   => $request->landmark2,
            'active_addr' => $request->active,
        ], fn ($value) => !is_null($value)));

        if (is_array($request->addresses)) {
            foreach ($request->addresses as $addressData) {
                Address::create([
                    'user_id'     => $request->user_id,
                    'name'        => $addressData['name'] ?? null,
                    'phone'       => $addressData['phone'] ?? null,
                    'address'     => $addressData['address'] ?? null,
                    'pincode'     => $addressData['pincode'] ?? null,
                    'landmark'    => $addressData['landmark'] ?? null,
                    'instruction' => $addressData['instruction'] ?? null,
                    'type'        => $addressData['type'] ?? 'home',
                    'status'      => $addressData['status'] ?? 1,
                    'latitude'    => $addressData['latitude'] ?? null,
                    'longitude'   => $addressData['longitude'] ?? null,
                ]);
            }
        }

        return $this->success(null, 'Profile updated');
    }

    private function issueLoginResponse(User $user, string $message)
    {
        $token  = $user->createToken('api-token')->plainTextToken;
        $wallet = Wallet::where('user_id', $user->id)
            ->selectRaw('COALESCE((SUM(debit) - SUM(credit)), 0) as balance')
            ->first();

        return response([
            'token'   => $token,
            'user'    => new UserResource($user),
            'wallet'  => $wallet,
            'message' => $message,
        ]);
    }

    public function updateName(Request $request)
    {
        $user = User::find($request->id);
        if (!$user) {
            return $this->error('User not found', 404);
        }

        $user->update(['name' => $request->name]);

        return $this->issueLoginResponse($user, 'Name updated successfully');
    }

    public function updateEmail(Request $request)
    {
        $user = User::find($request->id);
        if (!$user) {
            return $this->error('User not found', 404);
        }

        $user->update(['email' => $request->email]);

        return $this->issueLoginResponse($user, 'Email updated successfully');
    }

    // Used by the forgot-password flow: resets a password by email with no prior auth.
    public function updatePasswordByEmail(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email|exists:users,email',
            'password'              => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        return $this->issueLoginResponse($user, 'Password updated successfully');
    }

    public function checkUserByMobile(Request $request)
    {
        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            return response(['message' => 'Mobile number not registered'], 404);
        }

        if ($user->isDeactivated()) {
            return response(['message' => 'This account has been deactivated. Please contact support.'], 403);
        }

        return $this->issueLoginResponse($user, 'Mobile number already registered');
    }

    public function redeemCoins(Request $request)
    {
        $userId  = $request->user_id;
        $balance = LoyaltyTransaction::balanceFor($userId);
        $minRequired = LoyaltySetting::current()->min_points_to_convert;

        if ($balance < $minRequired || $balance <= 0) {
            return response(['message' => 'No coins to redeem'], 400);
        }

        \DB::transaction(function () use ($userId, $balance) {
            LoyaltyTransaction::create([
                'user_id'     => $userId,
                'points'      => -$balance,
                'type'        => 'redeemed',
                'description' => 'Converted to wallet balance',
            ]);

            Wallet::create([
                'user_id'     => $userId,
                'debit'       => $balance,
                'date'        => Carbon::today(),
                'description' => 'Coins redeemed to wallet',
            ]);
        });

        $walletBalance = Wallet::where('user_id', $userId)
            ->selectRaw('COALESCE(SUM(debit) - SUM(credit), 0) as balance')
            ->value('balance');

        return $this->success([
            'converted_points' => $balance,
            'coin'             => 0,
            'wallet_balance'   => $walletBalance,
        ], 'Coins redeemed successfully');
    }

    public function activeConversions()
    {
        $loyalty  = LoyaltySetting::current();
        $referral = ReferralSetting::current();

        return $this->success([
            'conversion_values' => [[
                'coin_conversion_rate'      => 1,
                'referrer_reward_points'    => $referral->reward_amount,
                'referee_reward_points'     => 0,
                'minimum_applicable_amount' => $loyalty->points_per_amount,
            ]],
        ]);
    }

    public function newPassword(Request $request)
    {
        User::where('mobile', $request->mobile)->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->success(null, 'Password updated');
    }

    public function applyRewardPoints(Request $request)
    {
        return $this->success(['use_points' => $request->use_points]);
    }

    public function convertLoyaltyPoints(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        $userId  = $request->user_id;
        $balance = LoyaltyTransaction::balanceFor($userId);
        $minRequired = LoyaltySetting::current()->min_points_to_convert;

        if ($balance < $minRequired) {
            return $this->error(
                "You need at least {$minRequired} points to convert to wallet. You currently have {$balance}.",
                422
            );
        }

        \DB::transaction(function () use ($userId, $balance) {
            LoyaltyTransaction::create([
                'user_id'     => $userId,
                'points'      => -$balance,
                'type'        => 'redeemed',
                'description' => 'Converted to wallet balance',
            ]);

            Wallet::create([
                'user_id'     => $userId,
                'debit'       => $balance,
                'date'        => Carbon::today(),
                'description' => 'Loyalty points converted to wallet',
            ]);
        });

        $walletBalance = Wallet::where('user_id', $userId)
            ->selectRaw('COALESCE(SUM(debit) - SUM(credit), 0) as balance')
            ->value('balance');

        return $this->success([
            'converted_points' => $balance,
            'loyalty_points'   => 0,
            'wallet_balance'   => $walletBalance,
        ], 'Loyalty points converted to wallet');
    }

    public function deactivateUser(Request $request)
    {
        $user = User::find($request->user_id);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        // Email/mobile are left untouched (no longer prefixed) so an admin can
        // cleanly restore the account later — mangling them made that a
        // one-way trip and risked double-prefixing on repeat calls.
        $user->update(['status' => User::STATUS_DEACTIVATED]);

        return $this->success(null, 'User deactivated');
    }
}
