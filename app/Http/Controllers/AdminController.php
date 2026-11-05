<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
}
