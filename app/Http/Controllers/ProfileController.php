<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View; // Cambiar Inertia\Response por View

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();
        $djs = User::where('role', 'worker')->orderBy('name')->get();
        $recentCollections = Collection::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        return view('profile.account', compact('djs','categories', 'recentCategories', 'recentCollections'));
    }

    public function billing(Request $request): View
    {
        $categories = Category::where('show_in_landing', true)->orderBy('name')->get();
        $djs = User::where('role', 'worker')->orderBy('name')->get();
        $recentCollections = Collection::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $recentCategories = Category::orderBy('created_at', 'desc')->take(5)->get()->filter(function ($item) {
            return $item->files()->count() > 0;
        });
        $plans = Plan::orderBy('price')->get();
        $orders = Order::where('user_id', Auth::user()->id)->orderBy('paid_at', 'desc')->get();
        return view('profile.billing', compact('djs','categories', 'plans', 'orders', 'recentCategories', 'recentCollections'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $path = $request->file('photo')->store('images', 's3');
        Storage::disk('s3')->setVisibility($path, 'public');

        $user = User::find(Auth::user()->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->photo = $path;
        $user->paypal_email = $request->paypal_email;

        $user->save();
        $success = "Información modificada correctamente";
        return redirect()->back()->with('success', $success);
    }

    public function restorePhoto(){
        $user = User::find(Auth::user()->id);
        $user->photo = null;
        $user->save();
        $success = "Imagen Reestablecida";
        return redirect()->back()->with('success', $success);
    }

    public function updateBilling(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $billing = $user->billing;
        if (!$billing) {
            $billing = new Billing();
            $billing->user_id = $user->id;
        }
        $billing->phone = $request->phone;
        $billing->address = $request->address;
        $billing->postal = $request->postal;
        $billing->country = $request->country;


        $billing->save();
        $success = "Información modificada correctamente";
        return redirect()->back()->with('success', $success);

    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        if (Hash::check($request->currentPassword, $user->password)) {
            if ($request->newPassword === $request->confirmPassword) {
                $user->password = Hash::make($request->newPassword);
                $user->save();
            } else {
                $error = "Por favor confirme correctamente la nueva contraseña";
                return redirect()->back()->with('error', $error);
            }
        } else {
            $error = "La contraseña actual introducida no es correcta";
            return redirect()->back()->with('error', $error);
        }

        $success = "La contraseña ha sido cambiada correctamente";
        return redirect()->back()->with('success', $success);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = User::find(Auth::user()->id);

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function getBillingLink(Request $request)
    {
        $user = $request->user();
        $user->createOrGetStripeCustomer();
        $url = $user->billingPortalUrl(route('profile.billing'));
        return redirect()->away($url);
    }
}
