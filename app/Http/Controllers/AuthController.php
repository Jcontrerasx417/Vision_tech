<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Models\Carrito;
use App\Models\Proveedor;
use App\Models\ProveedorPerfil;
use App\Models\SolicitudProveedor;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(AuthRegisterRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'] ?? 'cliente',
            ]);
            Carrito::firstOrCreate(['user_id' => $user->id]);
            if ($user->role === 'proveedor') {
                Proveedor::create([
                    'user_id' => $user->id,
                    'nombre' => $data['nombre_tienda'],
                    'contacto' => $user->name,
                    'email' => $user->email,
                ]);
                $proveedor = $user->proveedor()->first();
                ProveedorPerfil::create([
                    'user_id' => $user->id,
                    'proveedor_id' => $proveedor->id,
                    'nombre_comercial' => $data['nombre_tienda'],
                    'estado' => 'pendiente',
                ]);
                SolicitudProveedor::create([
                    'user_id' => $user->id,
                    'proveedor_id' => $proveedor->id,
                    'estado' => 'pendiente',
                    'mensaje' => 'Solicitud generada desde el registro de proveedor.',
                ]);
            }
            Auth::login($user);

            return redirect()->route($this->homeRouteFor($user->role));
        } catch (\Throwable $exception) {
            Log::error('Error registrando usuario', ['error' => $exception->getMessage()]);
            return back()->withErrors('No fue posible registrar el usuario.')->withInput();
        }
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(AuthLoginRequest $request): RedirectResponse
    {
        if (! Auth::attempt($request->validated(), true)) {
            return back()->withErrors(['email' => 'Credenciales incorrectas.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        return redirect()->route($this->homeRouteFor(Auth::user()->role));
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function homeRouteFor(string $role): string
    {
        return match ($role) {
            'administrador', 'personal_logistico' => 'admin.dashboard',
            'proveedor' => 'proveedor.dashboard',
            default => 'cliente.pedidos',
        };
    }
}
