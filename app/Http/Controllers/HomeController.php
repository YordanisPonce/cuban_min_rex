<?php

namespace App\Http\Controllers;

use App\Enums\SectionEnum;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Collection;
use App\Models\File;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\ContactNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index()
    {
        $pageTitle = "Inicio";
        $plans = Plan::orderBy('price')->get();
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();
        $djs = User::whereHas('files')->orderBy('name')->get();
        $artistCollections = File::where('original_file', 'LIKE', '%.zip')->where('status', 'active')
            ->whereJsonContains('sections', SectionEnum::MAIN->value)->orderBy('created_at', 'desc')->take(6)->get();
        $newItems = File::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->whereNot('original_file', 'LIKE', '%.zip')
            ->where('status', 'active')
            ->whereJsonContains('sections', SectionEnum::MAIN->value)
            ->orderBy('created_at', 'desc')->take(10)->get();
        if ($newItems->count() == 0) {
            $newItems = File::
                whereNot('original_file', 'LIKE', '%.zip')
                ->where('status', 'active')
                ->whereJsonContains('sections', SectionEnum::MAIN->value)
                ->orderBy('created_at', 'desc')->take(10)->get();
        }
        $tops = File::where('status', 'active')
            ->whereJsonContains('sections', SectionEnum::MAIN->value)->whereNot('original_file', 'LIKE', '%.zip')->orderBy('download_count', 'desc')->take(10)->get();
        $recentDjs = User::whereNot('role', 'user')->orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $ctg = Category::orderBy('name')->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });

        return view('home', compact('pageTitle', 'plans', 'ctg', 'djs', 'categories', 'artistCollections', 'newItems', 'tops', 'recentCategories', 'recentDjs'));
    }

    public function faq()
    {

        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();
        $djs = User::whereHas('files')->orderBy('name')->get();

        $recentDjs = User::whereNot('role', 'user')->orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
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

        $djs = User::whereHas('files')->orderBy('name')->get();

        $recentDjs = User::whereNot('role', 'user')->orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });

        return view('contact', compact('djs', 'categories', 'recentCategories', 'recentCollections'));
    }

    public function radio()
    {
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();

        $djs = User::whereHas('files')->orderBy('name')->get();

        $recentDjs = User::whereNot('role', 'user')->orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });

        $mixes = File::section(SectionEnum::CUBANDJS->value)
            ->where('status', 'active')
            ->whereNot('original_file', 'LIKE', '%.zip')
            ->orderBy('created_at', 'desc')
            ->paginate(30)->withQueryString();

        $mixesPlayList = File::section(SectionEnum::CUBANDJS->value)
            ->where('status', 'active')
            ->whereNot('original_file', 'LIKE', '%.zip')
            ->orderBy('created_at', 'desc')
            ->get()->map(fn($f) => [
                'id' => $f->id,
                'url' => Storage::disk('s3')->url($f->url ?? $f->file), // adapta si ya guardas rutas absolutas
                'title' => $f->title ?? $f->name,
            ]);

        $mixes->getCollection()->transform(function ($file) {
            $isZip = pathinfo(Storage::disk('s3')->url($file->url ?? $file->original_file), PATHINFO_EXTENSION) === 'zip';
            return [
                'id' => $file->id,
                'date' => $file->created_at,
                'user' => $file->user->name,
                'name' => $file->name,
                'logotipe' => $file->user->photo,
                'bpm' => $file->bpm,
                'collection' => $file->collection ? $file->collection->name : null,
                'categories' => $file->categories ?? [],
                'price' => $file->price,
                'url' => route('file.play', [$file->collection ? $file->collection->id : 'none', $file->id]),
                'isZip' => $isZip,
                'ext' => pathinfo(Storage::disk('s3')->url($file->url ?? $file->file), PATHINFO_EXTENSION),
            ];
        });

        $lives = File::section(SectionEnum::CUBANDJS_LIVE_SESSIONS->value)
            ->where('status', 'active')
            ->whereNot('original_file', 'LIKE', '%.zip')
            ->orderBy('created_at', 'desc')
            ->paginate(30)->withQueryString();
        
        $livesPlayList = File::section(SectionEnum::CUBANDJS_LIVE_SESSIONS->value)
            ->where('status', 'active')
            ->whereNot('original_file', 'LIKE', '%.zip')
            ->orderBy('created_at', 'desc')
            ->get()->map(fn($f) => [
                'id' => $f->id,
                'url' => Storage::disk('s3')->url($f->url ?? $f->file), // adapta si ya guardas rutas absolutas
                'title' => $f->title ?? $f->name,
            ]);
        
        $lives->getCollection()->transform(function ($file) {
            $isZip = pathinfo(Storage::disk('s3')->url($file->url ?? $file->original_file), PATHINFO_EXTENSION) === 'zip';
            return [
                'id' => $file->id,
                'date' => $file->created_at,
                'user' => $file->user->name,
                'name' => $file->name,
                'logotipe' => $file->user->photo,
                'bpm' => $file->bpm,
                'collection' => $file->collection ? $file->collection->name : null,
                'categories' => $file->categories ?? [],
                'price' => $file->price,
                'url' => route('file.play', [$file->collection ? $file->collection->id : 'none', $file->id]),
                'isZip' => $isZip,
                'ext' => pathinfo(Storage::disk('s3')->url($file->url ?? $file->file), PATHINFO_EXTENSION),
            ];
        });

        $allCategories = Category::orderBy('name')->get();
        $allRemixers = User::whereHas('files', function ($query) {
            $query->where('name', 'like', '%%');
        })->get();

        $setting = Setting::first();

        $activeCUPPayment = '';

        if ($setting) {
            $activeCUPPayment = !(is_null($setting->credit_card_info) | is_null($setting->confirmation_phone) | is_null($setting->confirmation_email) | is_null($setting->currency_convertion_rate));
        }

        return view('radio', compact('djs', 'categories', 'recentCategories', 'recentDjs', 'lives', 'livesPlayList', 'mixes', 'mixesPlayList', 'allCategories', 'allRemixers', 'activeCUPPayment'));
    }

    public function plan()
    {
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();
        $djs = User::whereHas('files')->orderBy('name')->get();
        $plans = Plan::orderBy('price')->get();

        $recentDjs = User::whereNot('role', 'user')->orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });

        return view('plans', compact('djs', 'plans', 'categories', 'recentCategories', 'recentDjs'));
    }

    public function dj($id)
    {
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();
        $djs = User::whereHas('files')->orderBy('name')->get();
        $recentDjs = User::whereNot('role', 'user')->orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });

        $name = request()->get("search") ?? "";
        $category = request()->get("categories") ?? "";

        $results = File::where('name', 'like', '%' . $name . '%')
            ->where('status', 'active')
            ->whereJsonContains('sections', SectionEnum::MAIN->value)
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('name', 'like', '%' . $category . '%');
            })
            ->whereHas('user', function ($q) use ($id) {
                $q->where('id', $id);
            })
            ->with(['user', 'category']) // Carga las relaciones
            ->orderBy('created_at', 'desc')
            ->paginate(30)->withQueryString();

        $playList = File::where('name', 'like', '%' . $name . '%')
            ->where('status', 'active')
            ->whereJsonContains('sections', SectionEnum::MAIN->value)
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('name', 'like', '%' . $category . '%');
            })
            ->whereHas('user', function ($q) use ($id) {
                $q->where('id', $id);
            })
            ->with(['user', 'category']) // Carga las relaciones
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($f) => [
                'id' => $f->id,
                'url' => Storage::disk('s3')->url($f->url ?? $f->file), // adapta si ya guardas rutas absolutas
                'title' => $f->title ?? $f->name,
            ]);

        $results->getCollection()->transform(function ($file) {
            $isZip = pathinfo(Storage::disk('s3')->url($file->url ?? $file->original_file), PATHINFO_EXTENSION) === 'zip';
            return [
                'id' => $file->id,
                'date' => $file->created_at,
                'user' => $file->user->name,
                'logotipe' => $file->user->photo,
                'name' => $file->name,
                'bpm' => $file->bpm,
                'collection' => $file->collection ? $file->collection->name : null,
                'categories' => $file->categories ?? [],
                'price' => $file->price,
                'url' => route('file.play', [$file->collection ? $file->collection->id : 'none', $file->id]),
                'isZip' => $isZip
            ];
        });

        $dj = User::find($id);


        $allCategories = Category::orderBy('name')->get();

        return view('search', compact('dj', 'results', 'djs', 'categories', 'recentCategories', 'recentDjs', 'allCategories', 'playList'));
    }

    public function sendContactForm(Request $request)
    {

        $name = $request->fullname ?? 'Anónimo';
        $email = $request->email ?? 'Desconocido';
        $message = $request->message ?? 'Mensaje vacío';

        try {
            $admins = User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                $admin->notify(new ContactNotification($name, $email, $message));
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Se ha enviado el formulario exitosamente, el personal de soporte se pondra en contacto con usted.');
    }

    public function remixes(Request $request)
    {
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();
        $djs = User::whereHas('files')->orderBy('name')->get();
        $recentDjs = User::whereNot('role', 'user')->orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });

        $name = request()->get("search") ?? "";
        $category = request()->get("categories") ?? "";
        $remixers = request()->get("remixers") ?? "";

        $results = File::whereJsonContains('sections', SectionEnum::MAIN->value)
            ->where('status', 'active')
            ->where('name', 'like', '%' . $name . '%')
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('name', 'like', '%' . $category . '%');
            })
            ->whereHas('user', function ($query) use ($remixers) {
                $query->where('name', 'like', '%' . $remixers . '%');
            })
            ->with(['user', 'category']) // Carga las relaciones
            ->orderBy('created_at', 'desc')
            ->paginate(30)->withQueryString();

        $playList = File::whereJsonContains('sections', SectionEnum::MAIN->value)
            ->where('status', 'active')
            ->where('name', 'like', '%' . $name . '%')
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('name', 'like', '%' . $category . '%');
            })
            ->whereHas('user', function ($query) use ($remixers) {
                $query->where('name', 'like', '%' . $remixers . '%');
            })
            ->with(['user', 'category']) // Carga las relaciones
            ->orderBy('created_at', 'desc')
            ->get()->map(fn($f) => [
                'id' => $f->id,
                'url' => Storage::disk('s3')->url($f->url ?? $f->file), // adapta si ya guardas rutas absolutas
                'title' => $f->title ?? $f->name,
            ]);

        $results->getCollection()->transform(function ($file) {
            $isZip = pathinfo(Storage::disk('s3')->url($file->url ?? $file->original_file), PATHINFO_EXTENSION) === 'zip';
            return [
                'id' => $file->id,
                'date' => $file->created_at,
                'user' => $file->user->name,
                'name' => $file->name,
                'logotipe' => $file->user->photo,
                'bpm' => $file->bpm,
                'collection' => $file->collection ? $file->collection->name : null,
                'categories' => $file->categories ?? [],
                'price' => $file->price,
                'url' => route('file.play', [$file->collection ? $file->collection->id : 'none', $file->id]),
                'isZip' => $isZip,
                'ext' => pathinfo(Storage::disk('s3')->url($file->url ?? $file->file), PATHINFO_EXTENSION),
            ];
        });

        $allCategories = Category::orderBy('name')->get();
        $allRemixers = User::whereHas('files', function ($query) {
            $query->where('name', 'like', '%%');
        })->get();

        $remixes = true;

        return view('search', compact('results', 'djs', 'remixes', 'categories', 'recentCategories', 'recentDjs', 'allCategories', 'allRemixers', 'playList'));
    }

    public function videos(Request $request)
    {
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();
        $djs = User::whereHas('files')->orderBy('name')->get();
        $recentDjs = User::whereNot('role', 'user')->orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });

        $name = request()->get("search") ?? "";
        $category = request()->get("categories") ?? "";
        $remixers = request()->get("remixers") ?? "";

        $videoEXT = ['.mp4', '.avi', '.mov', '.wmv', '.mkv'];

        $results = File::videos()->whereJsonContains('sections', SectionEnum::MAIN->value)
            ->where('status', 'active')
            ->where('name', 'like', '%' . $name . '%')
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('name', 'like', '%' . $category . '%');
            })
            ->whereHas('user', function ($query) use ($remixers) {
                $query->where('name', 'like', '%' . $remixers . '%');
            })
            ->with(['user', 'category']) // Carga las relaciones
            ->orderBy('created_at', 'desc')
            ->paginate(30)->withQueryString();

        $playList = File::videos()->whereJsonContains('sections', SectionEnum::MAIN->value)
            ->where('status', 'active')
            ->where('name', 'like', '%' . $name . '%')
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('name', 'like', '%' . $category . '%');
            })
            ->whereHas('user', function ($query) use ($remixers) {
                $query->where('name', 'like', '%' . $remixers . '%');
            })
            ->with(['user', 'category']) // Carga las relaciones
            ->orderBy('created_at', 'desc')
            ->get()->map(fn($f) => [
                'id' => $f->id,
                'url' => Storage::disk('s3')->url($f->url ?? $f->file), // adapta si ya guardas rutas absolutas
                'title' => $f->title ?? $f->name,
            ]);

        $results->getCollection()->transform(function ($file) {
            $ext = pathinfo(Storage::disk('s3')->url($file->url ?? $file->original_file), PATHINFO_EXTENSION);
            return [
                'id' => $file->id,
                'date' => $file->created_at,
                'user' => $file->user->name,
                'name' => $file->name,
                'logotipe' => $file->user->photo,
                'bpm' => $file->bpm,
                'collection' => $file->collection ? $file->collection->name : null,
                'categories' => $file->categories ?? [],
                'price' => $file->price,
                'url' => route('file.play', [$file->collection ? $file->collection->id : 'none', $file->id]),
                'isZip' => $ext === 'zip',
                'ext' => $ext,
            ];
        });

        $allCategories = Category::orderBy('name')->get();
        $allRemixers = User::whereHas('files', function ($query) {
            $query->where('name', 'like', '%%');
        })->get();

        $remixes = true;

        return view('search', compact('results', 'djs', 'remixes', 'categories', 'recentCategories', 'recentDjs', 'allCategories', 'allRemixers', 'playList'));
    }

    public function cart(Request $request)
    {
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();
        $djs = User::whereHas('files')->orderBy('name')->get();
        $recentDjs = User::whereNot('role', 'user')->orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });

        $cart = [];

        foreach (Cart::get_current_cart()->items ?? [] as $key => $value) {
            $file = File::find($value);
            array_push($cart, $file);
        }

        return view('cart', compact('cart', 'categories', 'djs', 'recentCategories', 'recentDjs'));
    }
}
