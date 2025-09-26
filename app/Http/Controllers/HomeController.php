<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Plan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $pageTitle = "Inicio";
        $plans = Plan::orderBy('price')->get();
        $categories = Category::where('show_in_landing', true)->get();
        $ctgSlides = Category::take(10)->get();

        $ctgSlides = $ctgSlides->filter(function ($item) {
            return $item->collections()->count() > 0;
        });

        return view('home', compact('pageTitle', 'plans', 'categories', 'ctgSlides'));
    }

    public function faq()
    {
        $categories = Category::where('show_in_landing', true)->get();

        return view('faq', compact('categories'));
    }

    public function contact()
    {
        $categories = Category::where('show_in_landing', true)->get();

        return view('contact', compact('categories'));
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
