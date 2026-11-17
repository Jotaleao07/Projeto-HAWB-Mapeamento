<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $pendingDoctors = User::where('role', User::ROLE_DOCTOR)
            ->where('status', User::STATUS_PENDING)
            ->latest()
            ->get();

        $contacts = Contact::latest()->limit(30)->get();

        $stats = [
            'users' => User::count(),
            'doctors' => User::where('role', User::ROLE_DOCTOR)->where('status', User::STATUS_APPROVED)->count(),
            'pending' => $pendingDoctors->count(),
            'contacts' => Contact::count(),
        ];

        return view('admin.dashboard', compact('pendingDoctors', 'contacts', 'stats'));
    }

    public function approve(Request $request, User $user): RedirectResponse
    {
        $user->update(['status' => User::STATUS_APPROVED]);

        return back()->with('success', "Conta de {$user->name} aprovada.");
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        $user->update(['status' => User::STATUS_REJECTED]);

        return back()->with('success', "Conta de {$user->name} recusada.");
    }

    public function toggleContact(Request $request, Contact $contact): RedirectResponse
    {
        $contact->update(['notified' => ! $contact->notified]);

        return back()->with('success', 'Status do contato atualizado.');
    }

    /** Exclui qualquer publicação (a política libera apenas administradores). */
    public function destroyPublication(Publication $publication): RedirectResponse
    {
        Gate::authorize('delete', $publication);

        $publication->delete();

        return redirect()->route('chat.index')->with('success', 'Publicação excluída com sucesso.');
    }

    // ---------- Registro de contas (tarefa 5) ----------

    /**
     * Registro de contas: todos os pacientes e médicos.
     * Contas de administrador não são listadas nem podem ser bloqueadas/banidas.
     */
    public function accounts(): View
    {
        $users = User::where('role', '!=', User::ROLE_ADMIN)
            ->orderByDesc('created_at')
            ->get();

        return view('admin.contas', [
            'users' => $users,
            'stats' => [
                'total' => $users->count(),
                'pacientes' => $users->where('role', User::ROLE_PATIENT)->count(),
                'medicos' => $users->where('role', User::ROLE_DOCTOR)->count(),
                'bloqueados' => $users->whereNotNull('chat_blocked_at')->count(),
                'banidos' => $users->whereNotNull('banned_at')->count(),
            ],
        ]);
    }

    /** Bloqueia o chat com médicos da conta. */
    public function blockChat(User $user): RedirectResponse
    {
        if ($erro = $this->protectAdminAccount($user)) {
            return $erro;
        }

        $user->update(['chat_blocked_at' => now()]);

        return back()->with('success', "Chat de {$user->name} bloqueado.");
    }

    /** Desbloqueia o chat com médicos da conta. */
    public function unblockChat(User $user): RedirectResponse
    {
        if ($erro = $this->protectAdminAccount($user)) {
            return $erro;
        }

        $user->update(['chat_blocked_at' => null]);

        return back()->with('success', "Chat de {$user->name} desbloqueado.");
    }

    /**
     * Banimento: o motivo é OBRIGATÓRIO — ele é exibido na tela de ban.
     * Ao banir, o usuário é deslogado na próxima requisição (middleware "banned").
     */
    public function ban(Request $request, User $user): RedirectResponse
    {
        if ($erro = $this->protectAdminAccount($user)) {
            return $erro;
        }

        $motivo = trim((string) $request->input('motivo'));

        if (mb_strlen($motivo) < 5) {
            return back()->with('error', 'Escreva o motivo do banimento (mínimo de 5 caracteres) — ele será exibido para o usuário.');
        }

        $user->update([
            'banned_at' => now(),
            'ban_reason' => mb_substr($motivo, 0, 1000),
        ]);

        return back()->with('success', "Conta de {$user->name} banida. O usuário será deslogado e verá o motivo.");
    }

    /** Reativa uma conta banida. */
    public function unban(User $user): RedirectResponse
    {
        if ($erro = $this->protectAdminAccount($user)) {
            return $erro;
        }

        $user->update([
            'banned_at' => null,
            'ban_reason' => null,
        ]);

        return back()->with('success', "Conta de {$user->name} reativada.");
    }

    /** Contas de administrador são protegidas: nenhuma ação de bloqueio/ban. */
    private function protectAdminAccount(User $user): ?RedirectResponse
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Contas de administrador são protegidas e não podem ser bloqueadas ou banidas.');
        }

        return null;
    }
}
