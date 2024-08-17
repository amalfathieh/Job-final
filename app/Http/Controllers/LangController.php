<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


class LangController extends Controller
{
    //
    public function setLang($lang){
        Session::put('lang',$lang);
        return back();
    }
}
