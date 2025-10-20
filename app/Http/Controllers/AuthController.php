<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function authenticated(Request $request, $user)
    {
        $user->update(['last_login_at' => now()]);
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Intentar autenticación verificando también el campo 'active'
        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'active' => true  // Solo usuarios activos
        ], $request->filled('remember'))) {
            
            $request->session()->regenerate();
            
            // Redireccionar según el rol (opcional)
            return $this->redirectToDashboard();
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden o tu cuenta está inactiva.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

    /**
     * Redirecciona según el rol del usuario
     */
    private function redirectToDashboard()
    {
        $user = Auth::user();
        
        // Puedes personalizar estas redirecciones según tus necesidades
        switch ($user->role) {
            case 'admin':
                return redirect()->intended('/admin/dashboard');
            case 'technician':
                return redirect()->intended('/technician/dashboard');
            case 'support':
                return redirect()->intended('/support/dashboard');
            case 'customer':
                return redirect()->intended('/customer/dashboard');
            case 'read-only':
                return redirect()->intended('/readonly/dashboard');
            default:
                return redirect()->intended('/dashboard');
        }
    }
}