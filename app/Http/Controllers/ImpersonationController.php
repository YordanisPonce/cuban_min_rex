<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public const SESSION_KEY = 'impersonator_id';

    public function start(Request $request, User $user)
    {
        $admin = $request->user();

        abort_unless($admin && $admin->role === 'admin', 403);
        abort_if($request->session()->has(self::SESSION_KEY), 403, 'Ya estás impersonando a un usuario.');
        abort_if($user->is($admin) || $user->role === 'admin', 403, 'No puedes impersonar a este usuario.');

        $this->switchTo($request, $user);
        $request->session()->put(self::SESSION_KEY, $admin->getKey());

        return redirect()->route('home');
    }

    public function stop(Request $request)
    {
        $adminId = $request->session()->pull(self::SESSION_KEY);
        $admin = $adminId ? User::find($adminId) : null;

        abort_unless($admin && $admin->role === 'admin', 403);

        $this->switchTo($request, $admin);

        return redirect('/admin/users');
    }

    private function switchTo(Request $request, User $user): void
    {
        // AuthenticateSession compara este hash; hay que descartarlo al cambiar de usuario.
        $request->session()->forget('password_hash_' . Auth::getDefaultDriver());

        Auth::login($user);
        $request->session()->regenerate();
    }
}
