@extends('layouts.app')

@section('title', 'HAWB - Minhas conversas')

@section('content')
    <div class="page-container">
        <div class="section-header">
            <h1>{{ $user->isDoctor() ? 'Pacientes em contato' : 'Minhas conversas' }}</h1>
            <p class="subtitle">
                {{ $user->isDoctor()
                    ? 'Pacientes que iniciaram uma conversa com você.'
                    : 'Converse diretamente com os médicos da plataforma.' }}
            </p>
        </div>

        @if(! $user->isDoctor())
            <div class="card-actions" style="justify-content: center; margin-bottom: 26px;">
                <a href="{{ route('chat.index') }}" class="btn-secondary">
                    <i class="fa-solid fa-newspaper"></i> Ver publicações e escolher um médico
                </a>
            </div>
        @endif

        @if($conversations->isEmpty())
            <div class="empty-state">
                <i class="fa-solid fa-comments"></i>
                <p>Nenhuma conversa ainda.</p>
                @if(! $user->isDoctor())
                    <a href="{{ route('chat.index') }}" class="btn-primary"
                       style="text-decoration:none; display:inline-block; margin-top:16px;">
                        Explorar publicações médicas
                    </a>
                @endif
            </div>
        @else
            <div class="chat-list" style="max-width: 760px; margin: 0 auto;">
                @foreach($conversations as $item)
                    @php($c = $item['conversation'])
                    @php($other = $item['other'])
                    @php($last = $item['last'])

                    <a class="chat-item" href="{{ route('chat.conversation', $c) }}">
                        <div class="name">
                            {{ $other->name }}
                            @if($other->specialty)
                                <span class="tag" style="margin-left:6px;">{{ $other->specialty }}</span>
                            @endif
                        </div>
                        <div class="meta">
                            {{ $other->isDoctor() ? 'Médico' : 'Paciente' }}
                            · {{ $c->last_message_at?->diffForHumans() ?? $c->created_at->diffForHumans() }}
                        </div>
                        @if($last)
                            <div class="preview">
                                {{ $last->sender_id === auth()->id() ? 'Você: ' : '' }}{{ \Illuminate\Support\Str::limit($last->body, 70) }}
                            </div>
                        @else
                            <div class="preview">Conversa iniciada — diga olá!</div>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
