<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Category;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View; // Cambiar Inertia\Response por View

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $categories = Category::where('show_in_landing', true)->get();
        return view('profile.account', compact('categories'));
    }

    public function billing(Request $request): View
    {
        $categories = Category::where('show_in_landing', true)->get();
        $plans = Plan::orderBy('price')->get();
        $orders = Order::where('user_id', Auth::user()->id)->orderBy('paid_at', 'desc')->get();
        return view('profile.billing', compact('categories', 'plans', 'orders'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $categories = Category::where('show_in_landing', true)->get();

        $user = User::find(Auth::user()->id);
        $user->name = $request->name;
        $user->email = $request->email;
        try {
            $user->save();
        } catch (\Throwable $th) {
            $error = "El correo asignado ya pertenece a otro usuario";
            return view('profile.account', compact('categories', 'error'));
        }

        return Redirect::route('profile.edit');
    }

    public function updateBilling(Request $request): RedirectResponse
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

        return Redirect::route('profile.edit');
    }

    public function updatePassword(Request $request)
    {
        $categories = Category::where('show_in_landing', true)->get();

        $user = Auth::user();
        
        if(Hash::check($request->currentPassword, $user->password)){
            if($request->newPassword === $request->confirmPassword){
                $user->password = Hash::make($request->newPassword);
                $user->save();
            } else {
                $error = "Por favor confirme correctamente la nueva contraseña";
                return view('profile.account', compact('categories', 'error'));
            }
        }else{
            $error = "La contraseña actual introducida no es correcta";
            return view('profile.account', compact('categories', 'error'));
        }

        $success = "La contraseña ha sido cambiada correctamente";
        return view('profile.account', compact('categories', 'success'));
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
}
