<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletApiController extends Controller
{
    public function index()
    {
        $debitSum = Wallet::where('user_id', Auth::id())
        ->sum('debit');
        $creditSum = Wallet::where('user_id', Auth::id())
        ->sum('credit');
        $walletSum = $debitSum- $creditSum;
        return response()->json(['walletSum' => $walletSum]);
    }
    public function store(Request $request)
    {
        $wallet = Wallet::create([
            'user_id' => Auth::id(),
            'debit' => $request->debit,
            'date' => Carbon::today(),
            'description' => $request->description
        ]);

        return response()->json(['status'=>'success']);
    }
}
