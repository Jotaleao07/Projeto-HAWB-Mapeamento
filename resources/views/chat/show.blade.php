@extends('layouts.app')

@section('title', 'HAWB - '.$publication->title)

@section('content')
    <div class="page-container" style="max-width: 820px;">
        <a href="{{ route('chat.index') }}" class="btn-secondary" style="margin-bottom: 24px;">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>

        <article class="form-card" style="margin-top: 18px;">
            @if($publication->category)
                <span class="tag" style="display:inline-block; background:#e6f7ed; color:#009639; font-size:.75rem;
                      font-weight:700; padding:3px 10px; border-radius:999px; text-transform:uppercase;">
                    {{ $publication->category }}
                </span>
            @endif

            <h1 style="font-size: 1.7rem; color:#111827; margin: 12px 0 8px;">{{ $publication->title }}</h1>

            <div class="meta" style="color:#6b7280; font-size:.9rem; margin-bottom: 20px;">
                <i class="fa-solid fa-user-doctor" style="color:#009639;"></i>
                <strong>{{ $publication->author->name }}</strong>
                @if($publication->author->specialty)
                    · {{ $publication->author->specialty }}
                @endif
                · {{ $publication->created_at->format('d/m/Y H:i') }}
            </div>

            <div style="line-height: 1.8; color:#374151; font-size: 1rem; white-space: pre-wrap;">{{ $publication->content }}</div>

            <div class="alert alert-warning" style="margin: 26px 0 0;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                Conteúdo informativo. Não substitui consulta, diagnóstico ou tratamento médico.
            </div>

            @auth
                @if(auth()->user()->id !== $publication->user_id && ! auth()->user()->isDoctor())
                    <div class="card-actions" style="margin-top: 24px;">
                        <a href="{{ route('chat.start', $publication->author) }}" class="btn-primary"
                           style="text-decoration:none; display:inline-block;">
                            <i class="fa-solid fa-comments"></i> Conversar com {{ explode(' ', $publication->author->name)[0] }}
                        </a>
                    </div>
                @endif
            @else
                <div class="card-actions" style="margin-top: 24px;">
                    <a href="{{ route('login') }}" class="btn-secondary">Entrar para conversar</a>
                </div>
            @endauth
        </article>
    </div>
@endsection
