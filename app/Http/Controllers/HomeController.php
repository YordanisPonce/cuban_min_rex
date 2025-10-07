<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Plan;
use App\Models\User;
use App\Notifications\ContactNotification;
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
        $newItems = Collection::whereBetween('created_at', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])->orderBy('created_at', 'desc')->get()->filter(function($item){
            return $item->files()->count() > 0;
        });
        $recentCollections = Collection::orderBy('created_at', 'desc')->take(5)->get()->filter(function($item){
            return $item->files()->count() > 0;
        });
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

    public function sendContactForm(Request $request){

        $name = $request->fullname ?? 'Anónimo';
        $email = $request->email ?? 'Desconocido';
        $message = $request->message ?? 'Mensaje vacío';

        try{
            $admins = User::where('role', 'admin')->get();
            
            foreach ($admins as $admin) {
                $admin->notify(new ContactNotification($name, $email, $message));
            }
        } catch (\Throwable $e){
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Se ha enviado el formulario exitosamente, el personal de soporte se pondra en contacto con usted.');
    }
}
