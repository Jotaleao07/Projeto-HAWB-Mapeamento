@extends('layouts.app')

@section('title', 'HAWB - Cadastro')

@section('content')
    <div class="auth-wrapper">
        <div class="form-card">
            <h2>Criar conta</h2>
            <p class="form-hint" style="margin-bottom: 20px;">Pacientes acessam o chat imediatamente; médicos passam por aprovação.</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <label for="name">Nome completo</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="255" autofocus>
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required maxlength="255">
                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="role">Tipo de conta</label>
                    <select id="role" name="role" required>
                        <option value="patient" @selected(old('role', 'patient') === 'patient')>Sou paciente</option>
                        <option value="doctor" @selected(old('role') === 'doctor')>Sou médico</option>
                    </select>
                    @error('role')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div id="doctor-fields" style="display: {{ old('role') === 'doctor' ? 'block' : 'none' }};">
                    <div class="form-group">
                        <label for="specialty">Especialidade</label>
                        <input type="text" id="specialty" name="specialty" value="{{ old('specialty') }}"
                               maxlength="100" placeholder="Ex: Cardiologia">
                        @error('specialty')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="crm">CRM / Registro profissional</label>
                        <input type="text" id="crm" name="crm" value="{{ old('crm') }}"
                               maxlength="30" placeholder="Ex: CRM-BA 12345">
                        @error('crm')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-hint" style="margin-bottom: 14px;">
                        <i class="fa-solid fa-circle-info"></i>
                        Contas médicas são liberadas por um administrador antes de publicarem ou responderem.
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Senha</label>
                    <input type="password" id="password" name="password" required minlength="8">
                    @error('password')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirmar senha</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%;">Criar conta</button>
            </form>

            <p class="auth-alt">Já tem conta? <a href="{{ route('login') }}">Entrar</a></p>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const roleSelect = document.getElementById('role');
    const doctorFields = document.getElementById('doctor-fields');
    const toggle = () => {
        doctorFields.style.display = roleSelect.value === 'doctor' ? 'block' : 'none';
    };
    roleSelect.addEventListener('change', toggle);
    toggle();
</script>
@endpush
