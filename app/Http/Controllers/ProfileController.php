<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /** Página do perfil: dados, funcionalidades do papel e configurações. */
    public function show(Request $request): View
    {
        $user = $request->user();

        return view('profile.show', [
            'user' => $user,
            'preferences' => $user->a11yPreferences(),
        ]);
    }

    /** Atualiza o nome exibido na conta: exige a senha atual e tem bloqueio de 24h. */
    public function saveName(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'current_password'],
        ], [
            'password.required' => 'Digite sua senha atual para confirmar a alteração.',
            'password.current_password' => 'Senha incorreta. Confira sua senha e tente de novo.',
        ]);

        $user = $request->user();
        $novoNome = trim($data['name']);

        // Só conta como alteração quando o nome realmente muda.
        if ($novoNome !== $user->name) {
            if ($user->name_changed_at !== null) {
                $liberaEm = $user->name_changed_at->copy()->addHours(24);

                if ($liberaEm->isFuture()) {
                    $segundos = (int) abs(now()->diffInSeconds($liberaEm, false));
                    $horas = intdiv($segundos, 3600);
                    $minutos = intdiv($segundos % 3600, 60);

                    if ($horas > 0) {
                        $espera = "{$horas}h" . ($minutos > 0 ? " e {$minutos}min" : '');
                    } elseif ($minutos > 0) {
                        $espera = "{$minutos}min";
                    } else {
                        $espera = 'menos de 1 min';
                    }

                    return back()->withErrors([
                        'name' => "Você já alterou o nome recentemente. Só é possível alterar novamente em {$espera}.",
                    ])->withInput();
                }
            }

            $user->update([
                'name' => $novoNome,
                'name_changed_at' => now(),
            ]);

            return back()->with('success', 'Nome atualizado com sucesso.');
        }

        return back()->with('success', 'Nome já está atualizado.');
    }

    /**
     * Salva as configurações de acessibilidade (chamado por fetch ao alternar cada chave).
     * Os dados são normalizados contra os padrões: só chaves conhecidas entram no banco.
     */
    public function saveSettings(Request $request): JsonResponse
    {
        $incoming = $request->input('preferences');
        $incoming = is_array($incoming) ? $incoming : [];
        $clean = User::PREFERENCES_DEFAULT;

        $daltonismOptions = ['off', 'protanopia', 'deuteranopia', 'tritanopia'];

        foreach ($clean as $group => $options) {
            $groupData = is_array($incoming[$group] ?? null) ? $incoming[$group] : [];

            foreach ($options as $key => $default) {
                if ($key === 'daltonism') {
                    $value = $groupData[$key] ?? $default;
                    $clean[$group][$key] = in_array($value, $daltonismOptions, true) ? $value : 'off';

                    continue;
                }

                $clean[$group][$key] = filter_var($groupData[$key] ?? $default, FILTER_VALIDATE_BOOLEAN);
            }
        }

        $user = $request->user();
        $user->update(['preferences' => $clean]);

        return response()->json([
            'ok' => true,
            'class' => $user->fresh()->a11yClass(),
        ]);
    }
}
