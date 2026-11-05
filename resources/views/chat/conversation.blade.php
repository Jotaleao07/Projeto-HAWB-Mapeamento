@extends('layouts.app')

@section('title', 'HAWB - Conversa com '.$other->name)

@section('content')
    <div class="page-container">
        <div class="card-actions" style="margin-bottom: 20px;">
            <a href="{{ route('chat.conversations') }}" class="btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Voltar às conversas
            </a>
        </div>

        <div class="chat-layout">
            <div class="chat-list">
                <div class="info-card">
                    <span class="tag">{{ $other->isDoctor() ? 'Médico' : 'Paciente' }}</span>
                    <h3 style="margin-top: 8px;">{{ $other->name }}</h3>
                    @if($other->specialty)
                        <p class="meta">{{ $other->specialty }}</p>
                    @endif
                    @if($other->bio)
                        <p>{{ $other->bio }}</p>
                    @endif
                    <div class="meta" style="margin-top: 10px;">
                        <i class="fa-solid fa-shield-halved"></i>
                        Conversa privada entre vocês dois.
                    </div>
                </div>

                <div class="info-card">
                    <h3 style="font-size: .95rem;">Precisa de orientação geral?</h3>
                    <p style="font-size: .87rem;">Consulte as publicações da equipe médica.</p>
                    <div class="card-actions">
                        <a href="{{ route('chat.index') }}" class="btn-secondary" style="padding: 8px 16px; font-size: .87rem;">
                            Ver publicações
                        </a>
                    </div>
                </div>
            </div>

            <div class="chat-window">
                <div class="chat-head">
                    <div class="name">{{ $other->name }}</div>
                    <div class="meta">
                        {{ $other->specialty ?? ($other->isDoctor() ? 'Médico' : 'Paciente') }}
                        · em conversa
                    </div>
                </div>

                <div class="chat-body" id="chatBody">
                    @forelse($messages as $message)
                        <div class="bubble {{ $message->sender_id === auth()->id() ? 'mine' : '' }}" data-id="{{ $message->id }}">
                            <div class="who">{{ $message->sender_id === auth()->id() ? 'Você' : $message->sender->name }}</div>
                            <div class="text">{{ $message->body }}</div>
                            <div class="when">{{ $message->created_at->format('H:i') }}</div>
                        </div>
                    @empty
                        <div class="empty-state" style="padding: 30px 10px;">
                            <i class="fa-solid fa-hand-wave"></i>
                            <p>Diga olá para começar a conversa.</p>
                        </div>
                    @endforelse
                </div>

                <form class="chat-form" method="POST" action="{{ route('chat.sendMessage', $conversation) }}" id="chatForm">
                    @csrf
                    <input type="text" name="body" id="chatInput" placeholder="Escreva uma mensagem..."
                           maxlength="2000" autocomplete="off" required>
                    <button type="submit" class="btn-primary">Enviar</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const body = document.getElementById('chatBody');
    const form = document.getElementById('chatForm');
    const input = document.getElementById('chatInput');
    const url = @json(route('chat.messages', $conversation));
    let lastId = Math.max(0, ...[...body.querySelectorAll('.bubble')].map(b => Number(b.dataset.id) || 0));
    let sending = false;

    const scrollDown = () => { body.scrollTop = body.scrollHeight; };
    scrollDown();

    function render(m) {
        const div = document.createElement('div');
        div.className = 'bubble' + (m.mine ? ' mine' : '');
        div.dataset.id = m.id;

        const who = document.createElement('div');
        who.className = 'who';
        who.textContent = m.mine ? 'Você' : m.sender;

        const text = document.createElement('div');
        text.className = 'text';
        text.textContent = m.body;

        const when = document.createElement('div');
        when.className = 'when';
        when.textContent = m.time;

        div.append(who, text, when);
        body.appendChild(div);
    }

    async function poll() {
        if (sending) return;
        try {
            const res = await fetch(url + '?after=' + lastId, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) return;
            const data = await res.json();
            if (!data.messages || data.messages.length === 0) return;

            const nearBottom = body.scrollHeight - body.scrollTop - body.clientHeight < 140;
            data.messages.forEach(m => { render(m); lastId = Math.max(lastId, m.id); });
            if (nearBottom) scrollDown();
        } catch (e) { /* rede instável: tenta de novo no ciclo seguinte */ }
    }

    setInterval(poll, 3000);

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const text = input.value.trim();
        if (!text || sending) return;

        sending = true;
        const btn = form.querySelector('button');
        btn.disabled = true;

        try {
            const token = document.querySelector('meta[name="csrf-token"]').content;
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({ body: text })
            });

            if (res.ok) {
                input.value = '';
                await poll();
                scrollDown();
            }
        } catch (e) { /* ignora */ }

        sending = false;
        btn.disabled = false;
        input.focus();
    });
})();
</script>
@endpush
