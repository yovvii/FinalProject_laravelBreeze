<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil semua banner dari database, diurutkan
        $banners = Banner::orderBy('id', 'desc')->get(); 
        
        // Kirim variabel $banners ke view
        return view('landing_page', compact('banners'));
    }
}
