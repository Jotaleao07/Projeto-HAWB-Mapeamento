<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Conta banida: desloga na hora e envia para a tela de banimento
 * (que mostra o motivo definido pelo administrador).
 */
class EnsureUserIsNotBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && $user->banned_at !== null) {
            $reason = $user->ban_reason;

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Motivo persiste na sessão: o usuário vê a tela mesmo ao recarregar.
            $request->session()->put('ban_reason', $reason);

            return redirect()->route('banned.show');
        }

        return $next($request);
    }
}
