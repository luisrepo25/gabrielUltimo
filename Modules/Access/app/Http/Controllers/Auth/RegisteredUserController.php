<?php

namespace Modules\Access\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Modules\Access\Models\Cliente;
use Modules\Access\Models\Usuario;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'ci' => ['required', 'integer', 'unique:usuario,ci'],
            'nombre' => ['required', 'string', 'max:50'],
            'apellido' => ['required', 'string', 'max:50'],
            'sexo' => ['required', 'string', 'max:1'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:usuario,email'],
            'password' => ['required', 'confirmed', Rules\Password::min(8)->mixedCase()->numbers()],
        ]);

        $user = Usuario::create([
            'ci' => $request->ci,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'sexo' => $request->sexo,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tipoPersona' => 'C', // Usamos 'C' para Clientes según tu estándar
        ]);

        Cliente::firstOrCreate([
            'ci' => $user->ci,
        ], [
            'categoria' => null
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('inventario', absolute: false));
    }
}
