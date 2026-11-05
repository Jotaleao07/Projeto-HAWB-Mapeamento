<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ChatController extends Controller
{
    /** Feed público de publicações médicas. */
    public function index(Request $request): View
    {
        $publications = Publication::with('author')
            ->where('is_published', true)
            ->when($request->filled('q'), fn ($query, $term) => $query
                ->where(fn ($q) => $q
                    ->where('title', 'like', "%{$term}%")
                    ->orWhere('content', 'like', "%{$term}%")))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('chat.index', compact('publications'));
    }

    public function show(Publication $publication): View
    {
        abort_unless($publication->is_published, 404);
        $publication->load('author');

        return view('chat.show', compact('publication'));
    }

    /* ---------- Publicações (somente médico aprovado) ---------- */

    public function create(): View
    {
        return view('chat.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:5000'],
            'category' => ['nullable', 'string', 'max:100'],
        ]);

        $request->user()->publications()->create($data);

        return redirect()->route('chat.index')->with('success', 'Publicação criada com sucesso!');
    }

    /* ---------- Conversas 1-a-1 ---------- */

    /** Lista de conversas do usuário logado (paciente vê médicos; médico vê pacientes). */
    public function conversations(): View
    {
        $user = request()->user();

        $conversations = ($user->isDoctor() ? $user->doctorConversations() : $user->conversations())
            ->with(['patient', 'doctor', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->orderByDesc('last_message_at')
            ->get()
            ->map(fn (Conversation $c) => [
                'conversation' => $c,
                'other' => $c->otherParticipant($user),
                'last' => $c->messages->first(),
            ]);

        return view('chat.conversations', compact('conversations', 'user'));
    }

    public function conversation(Conversation $conversation): View
    {
        Gate::authorize('view', $conversation);

        $conversation->load(['patient', 'doctor']);
        $messages = $conversation->messages()->with('sender')->orderBy('created_at')->get();

        // Marca como lidas as mensagens recebidas
        $conversation->messages()
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('chat.conversation', [
            'conversation' => $conversation,
            'messages' => $messages,
            'other' => $conversation->otherParticipant(auth()->user()),
        ]);
    }

    public function sendMessage(Request $request, Conversation $conversation): RedirectResponse|JsonResponse
    {
        Gate::authorize('view', $conversation);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => auth()->id(),
            'body' => $data['body'],
        ]);

        $conversation->update(['last_message_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $message->id,
                'body' => $message->body,
                'mine' => true,
                'sender' => auth()->user()->name,
                'time' => $message->created_at->format('H:i'),
            ]);
        }

        return redirect()->route('chat.conversation', $conversation);
    }

    /**
     * Endpoint JSON consumido pelo polling da tela de conversa.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function messages(Conversation $conversation)
    {
        Gate::authorize('view', $conversation);

        $after = (int) request()->query('after', 0);

        $messages = $conversation->messages()
            ->with('sender:id,name,role,specialty')
            ->where('id', '>', $after)
            ->orderBy('id')
            ->get()
            ->map(fn (Message $m) => [
                'id' => $m->id,
                'body' => $m->body,
                'mine' => $m->sender_id === auth()->id(),
                'sender' => $m->sender->name,
                'time' => $m->created_at->format('H:i'),
            ]);

        // Marca como lidas as mensagens recebidas
        $conversation->messages()
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['messages' => $messages]);
    }

    /** Abre (ou cria) uma conversa com um médico específico. */
    public function startWith(User $doctor): RedirectResponse
    {
        abort_unless($doctor->isDoctor(), 404);

        $user = auth()->user();

        $conversation = Conversation::firstOrCreate(
            ['user_id' => $user->id, 'doctor_id' => $doctor->id],
            ['last_message_at' => now()],
        );

        return redirect()->route('chat.conversation', $conversation);
    }
}
