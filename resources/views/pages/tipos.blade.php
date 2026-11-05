@extends('layouts.app')

@section('title', 'HAWB - Especialidades Médicas')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/tipos.css') }}">
@endsection

@section('content')
    <main class="tipos-container">

        <section class="section-header">
            <div class="title-with-icon">
                <span class="main-icon">🩺</span>
                <h1>Especialidades Médicas</h1>
            </div>
            <p class="subtitle">Encontre especialistas e saiba onde marcar sua consulta</p>
        </section>

        <section class="especialidades-grid">
            @foreach([
                ['otorrino', 'Otorrinolaringologia', 'Especialidade médica que trata de doenças do ouvido, nariz e garganta.'],
                ['odonto', 'Odontologia', 'Atendimento odontológico para prevenção e tratamento de doenças bucais.'],
                ['gineco', 'Ginecologia e Pré-natal', 'Atendimento à saúde da mulher, acompanhamento gestacional e exames preventivos.'],
                ['pediatria', 'Pediatria', 'Atendimento médico integral especializado para bebês, crianças e adolescentes.'],
                ['cardio', 'Cardiologia', 'Especialidade voltada ao diagnóstico e tratamento do coração e sistema circulatório.'],
                ['clinica', 'Clínica Geral', 'Atendimento médico inicial, check-ups e manejo de patologias diversas.'],
                ['vacina', 'Vacinação', 'Serviços de imunização e atualização da caderneta para todas as faixas etárias.'],
            ] as [$key, $title, $desc])
                <div class="especialidade-card" data-target="{{ $key }}">
                    <div class="card-title-row">
                        <span class="search-icon">🔍</span>
                        <h3>{{ $title }}</h3>
                    </div>
                    <p>{{ $desc }}</p>
                </div>
            @endforeach
        </section>

        <section id="indicacoes-container" class="indicacoes-container" style="display: none;">
            <div class="indicacoes-header">
                <span class="info-icon">ℹ</span>
                <h2 id="indicacao-titulo">Detalhamento da Especialidade</h2>
            </div>

            <div class="indicacoes-content">
                <div class="indicacao-bloc">
                    <h4>O que faz?</h4>
                    <p id="indicacao-oque-faz"></p>
                </div>

                <div class="indicacao-bloc">
                    <h4>Doenças mais comuns tratadas:</h4>
                    <p id="indicacao-doencas"></p>
                </div>

                <div class="indicacao-bloc block-highlight">
                    <h4>Onde encontrar atendimento?</h4>
                    <p id="indicacao-onde"></p>
                </div>
            </div>
        </section>

        <section class="imc-section">
            <div class="imc-header">
                <span class="balance-icon">⚖️</span>
                <h2>Verificador de Obesidade (IMC)</h2>
            </div>
            <p class="imc-subtitle">Calcule seu Índice de Massa Corporal e receba orientações preventivas imediatas.</p>

            <form id="imc-form" class="imc-form">
                <div class="form-group">
                    <label for="peso">Peso (kg)</label>
                    <input type="number" id="peso" step="0.1" placeholder="Ex: 70" required>
                </div>

                <div class="form-group">
                    <label for="altura">Altura (cm)</label>
                    <input type="number" id="altura" placeholder="Ex: 170" required>
                </div>

                <div class="form-group">
                    <label for="idade">Idade</label>
                    <input type="number" id="idade" placeholder="Ex: 30" required>
                </div>

                <div class="form-group">
                    <label for="sexo">Sexo</label>
                    <select id="sexo" required>
                        <option value="" disabled selected>Selecionar</option>
                        <option value="M">Masculino</option>
                        <option value="F">Feminino</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" id="btn-calcular" class="btn-calc" disabled>
                        <span class="btn-icon">⚖️</span> Calcular IMC
                    </button>
                </div>
            </form>

            <div id="imc-resultado" class="imc-result-box" style="display: none;">
                <div class="result-header">
                    <h4>Seu Resultado Hospitalar</h4>
                </div>
                <div class="result-content">
                    <p>Seu IMC calculado é: <strong id="val-imc">--</strong></p>
                    <p>Classificação Atual: <strong id="class-imc">--</strong></p>
                    <div id="orientacao-imc" class="orientacao-box"></div>
                </div>
            </div>
        </section>

    </main>
@endsection

@section('scripts')
    <script src="{{ asset('js/tipos.js') }}"></script>
@endsection
