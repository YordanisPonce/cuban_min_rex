<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Throwable;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = auth()->user();

        $subs = $user->orders()->whereHas('plan')->where('status', 'paid')->count();

        $currentPlan = null;

        if ($user->hasActivePlan()) {
            if($user->current_plan_id) $currentPlan = $user->currentPlan->name;
            else {
                $plan = $user->orders()->whereHas('plan')->where('status','paid')->orderBy('created_at', 'desc')->first();
                if($plan) $currentPlan = $plan->name;
            }
        }

        Carbon::setLocale('es');

        $recentActivity = $user->orders()->orderBy('created_at', 'desc')->take(5)->get()->transform( function($o){
            return [
                'title' => $o->plan ? 'Compra/Renovación de Plan' : 'Compra de artículos',
                'type' => $o->plan ? 1 : 0,
                'description' => $o->plan ? $o->plan->name : $o->order_items->count().' artículos',
                'status' => $o->status,
                'amount' => $o->amount,
                'date' => Carbon::parse($o->created_at)->diffForHumans(now())
            ];
        });

        $index = 999;

        return view('profile.account', compact('user', 'subs', 'index', 'currentPlan', 'recentActivity'));
    }

    public function billing(Request $request): View
    {
        $user = auth()->user();

        $index = 999;

        return view('profile.billing', compact('user', 'index'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {   
        /*try{
        
            $photo = $request->file('photo');
            
            if($photo){
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($photo);
                $encoded = $image->encode(new WebpEncoder(quality: 65));
                $webpPath = 'images/'.Str::random().'.webp';
                $encoded->save(Storage::disk('public')->path($webpPath));

                $stream = fopen(Storage::disk('public')->path($webpPath), 'r');
                    Storage::disk('s3')->writeStream($webpPath, $stream);
                    if (is_resource($stream))
                        fclose($stream);
                
                Storage::disk('public')->delete($webpPath);
                
                $photo = $webpPath;
            }

            $cover = $request->file('cover');
            
            if($cover){
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($cover);
                $encoded = $image->encode(new WebpEncoder(quality: 100));
                $webpPath = 'images/'.Str::random().'.webp';
                $encoded->save(Storage::disk('public')->path($webpPath));

                $stream = fopen(Storage::disk('public')->path($webpPath), 'r');
                    Storage::disk('s3')->writeStream($webpPath, $stream);
                    if (is_resource($stream))
                        fclose($stream);
                
                Storage::disk('public')->delete($webpPath);
                
                $cover = $webpPath;
            }

            $user = User::find(Auth::user()->id);
            $user->name = $request->name;
            $user->email = $request->email;
            $photo && $user->photo = $photo;
            $cover && $user->cover = $cover;
            $user->paypal_email = $request->paypal_email;
            $user->bio = $request->bio;

            $user->save();

            //modificar el billing
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

            //modificar los sociales
            $socials = $user->socialLinks;
            if (!$socials) {
                $socials = new \App\Models\SocialLink();
                $socials->user_id = $user->id;
            }
            $socials->facebook = $request->facebook;
            $socials->twitter = $request->twitter;
            $socials->instagram = $request->instagram;
            $socials->youtube = $request->youtube;
            $socials->tiktok = $request->tiktok;
            $socials->spotify = $request->spotify;
            $socials->site = $request->site;
            $socials->save();

            $currentPassword = $request->current_password;
            $newPassword = $request->new_password;
            $confirmPassword = $request->confirm_password;

            if ($currentPassword && $newPassword && $confirmPassword) {
                if (Hash::check($currentPassword, $user->password)) {
                    if ($newPassword === $confirmPassword) {
                        $user->password = Hash::make($newPassword);
                        $user->save();
                    } else {
                        $error = "Por favor confirme correctamente la nueva contraseña";
                        return redirect()->back()->with('error', $error);
                    }
                } else {
                    $error = "La contraseña actual introducida no es correcta";
                    return redirect()->back()->with('error', $error);
                }
            }

            $success = "Información modificada correctamente";
            return redirect()->back()->with('success', $success);
        /*} catch(\Throwable $th){
            return redirect()->back()->with('error', 'Error al guardar la informacion: '.$th->getMessage());
        }*/
    }

    public function restorePhoto(){
        $user = User::find(Auth::user()->id);
        $user->photo = null;
        $user->cover = null;
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
