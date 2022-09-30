<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    /**
     * Home View
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function __invoke(Request $request)
    {
        return view('front.home');
    }
}
