@extends('layouts.app')

@section('title', 'HAWB - Chat com Médicos')

@section('content')
    <div class="page-container">
        <div class="section-header">
            <h1>Chat com Médicos</h1>
            <p class="subtitle">Orientações publicadas pela equipe médica — e um canal direto para conversar.</p>
        </div>

        <div class="card-actions" style="justify-content: center; margin-bottom: 28px;">
            <a href="{{ route('chat.conversations') }}" class="btn-secondary">
                <i class="fa-solid fa-comments"></i> Minhas conversas
            </a>
            @auth
                @if(auth()->user()->isDoctor())
                    <a href="{{ route('doctor.chat.create') }}" class="btn-primary" style="text-decoration:none; display:inline-block;">
                        <i class="fa-solid fa-pen"></i> Nova publicação
                    </a>
                @endif
            @endauth
        </div>

        <form method="GET" action="{{ route('chat.index') }}" style="max-width: 560px; margin: 0 auto 30px; display:flex; gap:10px;">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar publicação..."
                   style="flex:1; padding:11px 14px; border:1px solid #d1d5db; border-radius:8px; font-family:inherit;">
            <button type="submit" class="btn-primary">Buscar</button>
        </form>

        @if($publications->isEmpty())
            <div class="empty-state">
                <i class="fa-solid fa-newspaper"></i>
                <p>Nenhuma publicação encontrada.</p>
            </div>
        @else
            <div class="cards-grid">
                @foreach($publications as $publication)
                    <article class="info-card">
                        @if($publication->category)
                            <span class="tag">{{ $publication->category }}</span>
                        @endif
                        <h3 style="margin-top: 8px;">{{ $publication->title }}</h3>
                        <div class="meta">
                            <i class="fa-solid fa-user-doctor"></i>
                            {{ $publication->author->name }}
                            @if($publication->author->specialty)
                                · {{ $publication->author->specialty }}
                            @endif
                            · {{ $publication->created_at->format('d/m/Y') }}
                        </div>
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($publication->content), 160) }}</p>
                        <div class="card-actions">
                            <a href="{{ route('chat.show', $publication) }}" class="btn-secondary">Ler mais</a>
                            @auth
                                @if(auth()->user()->id !== $publication->user_id && ! auth()->user()->isDoctor())
                                    <a href="{{ route('chat.start', $publication->author) }}" class="btn-primary"
                                       style="text-decoration:none; display:inline-block; padding:10px 20px;">
                                        Conversar
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </article>
                @endforeach
            </div>

            {{ $publications->links() }}
        @endif
    </div>
@endsection
