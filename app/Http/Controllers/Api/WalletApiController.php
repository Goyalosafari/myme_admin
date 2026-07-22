<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletApiController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $balance = Wallet::where('user_id', Auth::id())
            ->selectRaw('COALESCE(SUM(debit) - SUM(credit), 0) as balance')
            ->value('balance');

        return $this->success(['balance' => $balance]);
    }

    public function store(Request $request)
    {
        Wallet::create([
            'user_id'     => Auth::id(),
            'debit'       => $request->debit,
            'date'        => Carbon::today(),
            'description' => $request->description,
        ]);

        return $this->success(null, 'Wallet updated');
    }
}
