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

    /** Atualiza o nome exibido na conta. */
    public function saveName(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $request->user()->update($data);

        return back()->with('success', 'Nome atualizado com sucesso.');
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
