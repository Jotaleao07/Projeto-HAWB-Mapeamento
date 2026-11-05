@extends('layouts.app')

@section('title', 'HAWB - Contato')

@section('content')
    <div class="page-container">
        <div class="section-header">
            <h1>Fale Conosco</h1>
            <p class="subtitle">Dúvidas, sugestões, críticas ou parcerias — envie sua mensagem para a equipe HAWB.</p>
        </div>

        <div class="form-card" style="max-width: 720px; margin: 0 auto;">
            <form method="POST" action="{{ route('contact.store') }}">
                @csrf

                <div class="form-group">
                    <label for="name">Nome completo</label>
                    <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name ?? '') }}"
                           required maxlength="255" placeholder="Seu nome">
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}"
                           required maxlength="255" placeholder="voce@exemplo.com">
                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="subject">Assunto</label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                           required maxlength="255" placeholder="Sobre o que você quer falar?">
                    @error('subject')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="message">Mensagem</label>
                    <textarea id="message" name="message" required maxlength="5000"
                              placeholder="Escreva aqui a sua mensagem...">{{ old('message') }}</textarea>
                    @error('message')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-hint" style="margin-bottom: 18px;">
                    <i class="fa-solid fa-shield-halved"></i>
                    Suas informações são usadas apenas para responder este contato.
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-paper-plane"></i> Enviar mensagem
                </button>
            </form>
        </div>
    </div>
@endsection
