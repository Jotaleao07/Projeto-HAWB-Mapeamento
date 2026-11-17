@extends('layouts.app')

@section('title', 'HAWB - Conta Banida')

@section('content')
    <main class="banned-page">
        <div class="banned-card" role="alert">
            <span class="banned-icon" aria-hidden="true">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </span>

            <h1>Ops, ocorreu um erro grave !</h1>

            <p class="banned-text">
                Infelizmente sua conta foi BANIDA dos nossos servidores, segue abaixo o motivo.
            </p>

            <div class="banned-reason">
                <span class="banned-reason-label">Motivo do administrador:</span>
                <p>{{ $reason }}</p>
            </div>

            <a href="{{ route('login') }}" class="btn-primary">Voltar para o login</a>
        </div>
    </main>
@endsection
