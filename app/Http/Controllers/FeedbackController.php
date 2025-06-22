<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    protected $feedback;

    public function __construct(Rating $feedback)
    {
        $this->feedback = $feedback;
    }

    public function index()
    {
        $datas = $this->feedback->all();
        return view('feedback',compact('datas'));
    }

}
