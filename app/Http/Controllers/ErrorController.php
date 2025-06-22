<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErrorController extends Controller
{
    public function serverError()
    {
        return view('error.server_error');
    }
}
