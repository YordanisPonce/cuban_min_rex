// <?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;

// class CustomLoginController extends Controller
// {
//     public function showLoginForm()
//     {
//         return view('auth.auth-login-cover');
//     }

//     public function login(Request $request)
//     {
//         $credentials = $request->only('email', 'password');

//         if (Auth::attempt($credentials, $request->filled('remember'))) {
//             $request->session()->regenerate();

//             // Redirigir según rol
//             $user = Auth::user();
//             return match($user->role) {
//                 'admin' => redirect('/admin'),
//                 default => redirect('/'),
//             };
//         }

//         return back()->withErrors([
//             'email' => 'Las credenciales no coinciden.',
//         ])->onlyInput('email');
//     }

//     public function logout(Request $request)
//     {
//         Auth::logout();
//         $request->session()->invalidate();
//         $request->session()->regenerateToken();

//         return redirect('/login');
//     }
//}
