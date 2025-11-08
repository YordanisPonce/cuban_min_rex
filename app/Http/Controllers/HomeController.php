<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\File;
use App\Models\Plan;
use App\Models\User;
use App\Notifications\ContactNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index()
    {
        $pageTitle = "Inicio";
        $plans = Plan::orderBy('price')->get();
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();
        $djs = User::where('role', 'worker')->orderBy('name')->get();
        $artistCollections = Collection::all()->filter(function($item){
            return $item->files()->count() > 0;
        });
        $newItems = File::whereBetween('created_at', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])->orderBy('created_at', 'desc')->get();
        $recentCollections = Collection::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });        
        $ctg = Category::orderBy('name')->get()->filter(function($item){
            return $item->files()->count() > 0;
        });
        return view('home', compact('pageTitle', 'plans', 'ctg', 'djs','categories', 'artistCollections', 'newItems', 'recentCategories', 'recentCollections'));
    }

    public function faq()
    {
        
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();
        $djs = User::where('role', 'worker')->orderBy('name')->get();
        
        $recentCollections = Collection::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });

        return view('faq', compact('djs', 'categories', 'recentCategories', 'recentCollections'));
    }

    public function contact()
    {
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();
        
        $djs = User::where('role', 'worker')->orderBy('name')->get();
        
        $recentCollections = Collection::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });

        return view('contact', compact('djs','categories', 'recentCategories', 'recentCollections'));
    }

    public function radio()
    {
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();
        
        $djs = User::where('role', 'worker')->orderBy('name')->get();
        
        $recentCollections = Collection::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });

        return view('radio', compact('djs','categories', 'recentCategories', 'recentCollections'));
    }

    public function plan()
    {
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();
        $djs = User::where('role', 'worker')->orderBy('name')->get();
        $plans = Plan::orderBy('price')->get();
        
        $recentCollections = Collection::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });

        return view('plans', compact('djs','plans','categories', 'recentCategories', 'recentCollections'));
    }

    public function dj($id)
    {
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();
        $djs = User::where('role', 'worker')->orderBy('name')->get();
        $recentCollections = Collection::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        
        $results = File::where(function ($query) use ($id) {
                $query->whereHas('user', function ($q) use ($id) {
                    $q->where('id', $id);
                });
            })
            ->with(['collection.category'])
            ->orderBy('created_at', 'desc')
            ->paginate(30);
        
        $name = request()->get("search");
        $category = request()->get("categories");
        $remixers = request()->get("remixers");
        if ($name || $category || $remixers) {
            $results = File::where('name', 'like', '%' . $name . '%')
                ->whereHas('category', function ($query) use ($category) {
                    $query->where('name', 'like', '%' . $category . '%');
                })
                ->whereHas('user', function ($query) use ($remixers) {
                    $query->where('name', 'like', '%' . $remixers . '%');
                })
                ->with(['user', 'category']) // Carga las relaciones
                ->orderBy('created_at', 'desc')
                ->paginate(30);
        }

        $results->getCollection()->transform(function ($file) {
            $isZip = pathinfo(Storage::disk('s3')->url($file->url ?? $file->file), PATHINFO_EXTENSION) === 'zip';
            return [
                'id' => $file->id,
                'date' => $file->created_at,
                'user' => $file->user->name,
                'name' => $file->name,
                'bpm' => $file->bpm,
                'collection' => $file->collection->name ?? null,
                'category' => $file->category->name ?? null,
                'price' => $file->price,
                'url' => route('file.play', [$file->collection ? $file->collection->id : 'none', $file->id]),
                'isZip' => $isZip
            ];
        });

        $dj = User::find($id);
        
        $playList = []; 

        foreach ($results as $f) {
            $file = File::find($f['id']);
            $track = $file->get()
                ->map(fn($f) => [
                    'id' => $f->id,
                    'url' => Storage::disk('s3')->url($f->url ?? $f->file), // adapta si ya guardas rutas absolutas
                    'title' => $f->title ?? $f->name,
                ]);
            array_push($playList, $track);
        }


        $allCategories = Category::all();

        return view('search', compact('dj','results', 'djs','categories', 'recentCategories', 'recentCollections', 'allCategories', 'playList'));
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

    public function remixes(Request $request)
    {
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();
        $djs = User::where('role', 'worker')->orderBy('name')->get();
        $recentCollections = Collection::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });  

        $results = File::orderBy('created_at', 'desc')->paginate(30);;

        $name = request()->get("search");
        $category = request()->get("categories");
        $remixers = request()->get("remixers");
        if ($name || $category || $remixers) {
            $results = File::where('name', 'like', '%' . $name . '%')
                ->whereHas('category', function ($query) use ($category) {
                    $query->where('name', 'like', '%' . $category . '%');
                })
                ->whereHas('user', function ($query) use ($remixers) {
                    $query->where('name', 'like', '%' . $remixers . '%');
                })
                ->with(['user', 'category']) // Carga las relaciones
                ->orderBy('created_at', 'desc')
                ->paginate(30);
        }

        $results->getCollection()->transform(function ($file) {
            $isZip = pathinfo(Storage::disk('s3')->url($file->url ?? $file->file), PATHINFO_EXTENSION) === 'zip';
            return [
                'id' => $file->id,
                'date' => $file->created_at,
                'user' => $file->user->name,
                'name' => $file->name,
                'bpm' => $file->bpm,
                'collection' => $file->collection->name ?? null,
                'category' => $file->category->name ?? null,
                'price' => $file->price,
                'url' => route('file.play', [$file->collection ? $file->collection->id : 'none', $file->id]),
                'isZip' => $isZip,
                'ext' => pathinfo(Storage::disk('s3')->url($file->url ?? $file->file), PATHINFO_EXTENSION),
            ];
        });
        
        $playList = []; 

        foreach ($results as $f) {
            $file = File::find($f['id']);
            $track = $file->get()
                ->map(fn($f) => [
                    'id' => $f->id,
                    'url' => Storage::disk('s3')->url($f->url ?? $f->file), // adapta si ya guardas rutas absolutas
                    'title' => $f->title ?? $f->name,
                ]);
            array_push($playList, $track);
        }

        $allCategories = Category::all();
        $allRemixers = User::whereHas('files', function ($query) {
            $query->where('name', 'like', '%%');
        })->get();

        $remixes = true;

        return view('search', compact('results', 'djs', 'remixes','categories', 'recentCategories', 'recentCollections', 'allCategories', 'allRemixers', 'playList'));
    }
}
