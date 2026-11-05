@extends('layouts.app')

@section('title', 'HAWB - Nova publicação')

@section('content')
    <div class="page-container" style="max-width: 720px;">
        <div class="section-header">
            <h1>Nova publicação</h1>
            <p class="subtitle">Compartilhe uma orientação com os usuários da plataforma.</p>
        </div>

        <div class="form-card">
            <form method="POST" action="{{ route('doctor.chat.store') }}">
                @csrf

                <div class="form-group">
                    <label for="title">Título</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required maxlength="255"
                           placeholder="Ex: Sinais de alerta de dengue">
                    @error('title')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="category">Categoria (opcional)</label>
                    <input type="text" id="category" name="category" value="{{ old('category') }}" maxlength="100"
                           placeholder="Ex: Pediatria, Urgência, Prevenção">
                    @error('category')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="content">Conteúdo</label>
                    <textarea id="content" name="content" required maxlength="5000"
                              placeholder="Escreva a orientação...">{{ old('content') }}</textarea>
                    @error('content')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="card-actions">
                    <button type="submit" class="btn-primary">Publicar</button>
                    <a href="{{ route('chat.index') }}" class="btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
