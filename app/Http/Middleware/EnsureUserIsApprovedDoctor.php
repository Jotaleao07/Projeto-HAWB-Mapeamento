<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApprovedDoctor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->isAdmin()) {
            return $next($request);
        }

        if ($user->isPendingDoctor()) {
            return redirect()->route('inicio')
                ->with('error', 'Sua conta de médico ainda está em análise. Aguarde a aprovação.');
        }

        if (! $user->isDoctor()) {
            abort(403, 'Esta área é exclusiva para médicos aprovados.');
        }

        return $next($request);
    }
}
