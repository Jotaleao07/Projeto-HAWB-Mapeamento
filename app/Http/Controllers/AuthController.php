<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('inicio'));
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:patient,doctor'],
            'specialty' => ['required_if:role,doctor', 'nullable', 'string', 'max:100'],
            'crm' => ['required_if:role,doctor', 'nullable', 'string', 'max:30'],
        ]);

        // Médicos entram como pendentes e só publicam após aprovação do admin.
        $isDoctor = $data['role'] === User::ROLE_DOCTOR;

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'specialty' => $data['specialty'] ?? null,
            'crm' => $data['crm'] ?? null,
            'status' => $isDoctor ? User::STATUS_PENDING : User::STATUS_APPROVED,
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        if ($isDoctor) {
            return redirect()->route('inicio')->with('success',
                'Cadastro enviado! Sua conta de médico aguarda aprovação do administrador.');
        }

        return redirect()->route('inicio')->with('success', 'Conta criada com sucesso!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('inicio');
    }
}
