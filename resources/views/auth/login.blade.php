@extends('layouts.app')

@section('title', 'HAWB - Entrar')

@section('content')
    <div class="auth-wrapper">
        <div class="form-card">
            <h2>Entrar</h2>
            <p class="form-hint" style="margin-bottom: 20px;">Acesse sua conta HAWB para conversar com médicos.</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="password">Senha</label>
                    <input type="password" id="password" name="password" required>
                    @error('password')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group" style="flex-direction: row; align-items: center; gap: 8px;">
                    <input type="checkbox" id="remember" name="remember" style="width: auto;">
                    <label for="remember" style="font-weight: 400;">Lembrar de mim</label>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%;">Entrar</button>
            </form>

            <p class="auth-alt">Ainda não tem conta? <a href="{{ route('register') }}">Cadastre-se</a></p>
        </div>
    </div>
@endsection
