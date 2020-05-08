<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    
    public function index(Request $request)
    {
        $params = $request->all();

        return view('web.chat.index');
    }


    public function index2(Request $request)
    {
        $params = $request->all();

        return view('web.chat.index2');
    }
}
