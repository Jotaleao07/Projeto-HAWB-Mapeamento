<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Chat bloqueado pelo administrador: impede o acesso às páginas de
 * chat com médicos até que o bloqueio seja removido no painel.
 */
class EnsureChatAllowed
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && $user->chat_blocked_at !== null) {
            return redirect()
                ->route('profile.show')
                ->with('error', 'Seu chat com médicos está bloqueado pelo administrador. '
                    . 'Você pode consultar as demais páginas do site normalmente.');
        }

        return $next($request);
    }
}
