<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $pageTitle = "Inicio";
        return view('home', compact('pageTitle'));
    }

    // Seccines del Home
    public function dashboard()
    {
        return view('dashboard');
    }

    public function about()
    {
        $pageTitle = "Acerca de nosotros";
        $teamMembers = [
            ["name" => "Ana García", "role" => "CEO"],
            ["name" => "Carlos López", "role" => "Desarrollador"]
        ];
        
        return view('about', compact('pageTitle', 'teamMembers'));
    }
}