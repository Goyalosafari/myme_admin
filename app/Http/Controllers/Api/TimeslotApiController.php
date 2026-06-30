<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TimeslotResource;
use App\Models\TimeSlot;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimeslotApiController extends Controller
{
    use ApiResponse;

    public function index()
    {
        return $this->success(TimeslotResource::collection(TimeSlot::all()));
    }

    public function getTimeSlotByDate(Request $request)
    {
        $now = Carbon::now();

        if ($request->date === $now->toDateString()) {
            $slots = TimeSlot::where('ref1', '>', $now->format('H'))->get();
        } else {
            $slots = TimeSlot::all();
        }

        return $this->success(TimeslotResource::collection($slots));
    }
}
