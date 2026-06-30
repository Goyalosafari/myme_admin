<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserApiController extends Controller
{
    use ApiResponse;

    public function index()
    {
        return $this->success(new UserResource(Auth::user()));
    }

    public function userInfo(Request $request)
    {
        $user = User::find($request->user_id);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        return $this->success(new UserResource($user));
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
