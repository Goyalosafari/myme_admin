<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\LoyaltySetting;
use App\Models\LoyaltyTransaction;
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
        $resource = (new UserResource(Auth::user()))->resolve();
        return response()->json(['success' => true, 'data' => $resource, 'user' => $resource]);
    }

    public function userInfo(Request $request)
    {
        $user = User::find($request->user_id);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        $resource = (new UserResource($user))->resolve();
        return response()->json(['success' => true, 'data' => $resource, 'user' => $resource]);
    }

    public function update(Request $request)
    {
        User::where('id', $request->user_id)->update([
            'name'        => $request->name,
            'mobile'      => $request->mobile,
            'address1'    => $request->address1,
            'address2'    => $request->address2,
            'pincode1'    => $request->pincode1,
            'pincode2'    => $request->pincode2,
            'landmark1'   => $request->landmark1,
            'landmark2'   => $request->landmark2,
            'active_addr' => $request->active,
        ]);

        return $this->success(null, 'Profile updated');
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

        $user->update([
            'email'  => 'deactivated-' . $user->email,
            'mobile' => '0000-' . $user->mobile,
            'status' => 2,
        ]);

        return $this->success(null, 'User deactivated');
    }
}
