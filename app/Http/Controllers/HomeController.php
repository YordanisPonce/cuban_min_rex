<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $pageTitle = "Inicio";
        $plans = Plan::orderBy('price')->get();
        $categories = Category::where('show_in_landing', true)->get();
        $artistCollections = Collection::all()->filter(function($item){
            return $item->files()->count() > 0;
        });
        $newItems = Collection::whereBetween('created_at', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])->orderBy('created_at', 'desc')->get();

        $recentCollections = Collection::orderBy('created_at', 'desc')->take(5)->get();
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get();
        $ctg = Category::all()->filter(function($item){
            return $item->files()->count() > 0;
        });

        return view('home', compact('pageTitle', 'plans', 'ctg', 'categories', 'artistCollections', 'newItems', 'recentCategories', 'recentCollections'));
    }

    public function faq()
    {
        $categories = Category::where('show_in_landing', true)->get();
        
        $recentCollections = Collection::orderBy('created_at', 'desc')->take(5)->get();
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get();

        return view('faq', compact('categories', 'recentCategories', 'recentCollections'));
    }

    public function contact()
    {
        $categories = Category::where('show_in_landing', true)->get();
        
        $recentCollections = Collection::orderBy('created_at', 'desc')->take(5)->get();
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get();

        return view('contact', compact('categories', 'recentCategories', 'recentCollections'));
    }

    public function plan()
    {
        $categories = Category::where('show_in_landing', true)->get();
        $plans = Plan::orderBy('price')->get();
        
        $recentCollections = Collection::orderBy('created_at', 'desc')->take(5)->get();
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get();

        return view('plans', compact('plans','categories', 'recentCategories', 'recentCollections'));
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
