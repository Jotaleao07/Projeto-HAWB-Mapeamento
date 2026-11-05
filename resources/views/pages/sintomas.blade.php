@extends('layouts.app')

@section('title', 'HAWB - Análise de Sintomas')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/sintomas.css') }}">
@endsection

@section('content')
    <main class="sintomas-main">
        <h1>Análise de Sintomas</h1>
        <p class="subtitle">Selecione os sintomas que você está sentindo</p>

        <div class="sintomas-grid">
            @foreach([
                ['dor-peito', 'emergência', 'Dor no peito intensa'],
                ['respirar', 'emergência', 'Dificuldade severa para respirar'],
                ['desmaio', 'emergência', 'Desmaio ou perda de consciência'],
                ['sangramento', 'emergência', 'Sangramento intenso'],
                ['avc', 'emergência', 'Sinais de AVC (fala arrastada, fraqueza)'],
                ['animais', 'emergência', 'Animais ou Bicho peçonhento'],
                ['febre-alta', 'upa', 'Febre alta persistente'],
                ['dor-intensa', 'upa', 'Dor intensa'],
                ['lesao', 'upa', 'Lesão ou trauma'],
                ['vomitos', 'upa', 'Vômitos persistentes'],
                ['dor-cabeca', 'ubs', 'Dor de cabeça'],
                ['resfriado', 'ubs', 'Resfriado'],
                ['dor-leve', 'ubs', 'Dor leve'],
                ['consulta', 'ubs', 'Consulta de rotina'],
            ] as [$value, $severity, $label])
                <label class="sintoma-card" data-severity="{{ $severity }}">
                    <input type="checkbox" value="{{ $value }}">
                    <span class="custom-checkbox"></span>
                    <span class="sintoma-label">{{ $label }}</span>
                </label>
            @endforeach
        </div>

        <button class="btn-analisar" id="btnAnalisar">Analisar Sintomas &rarr;</button>

        <div id="resultadoContainer" class="resultado-container">

            <div id="statusEmergencia" class="resultado-box box-emergencia">
                <div class="resultado-header">
                    <span class="icon-alert">!</span>
                    <h2>Recomendação: EMERGÊNCIA - SAMU 192</h2>
                </div>
                <div class="resultado-body">
                    <p class="warning-alert">! SITUAÇÃO GRAVE! Ligue imediatamente para o SAMU 192 ou vá ao hospital mais próximo.</p>
                    <p>Seus sintomas indicam uma emergência médica que requer atendimento imediato. Não perca tempo.</p>
                    <p class="atendimento-lista"><strong>EMERGÊNCIA atende:</strong> AVC, infarto, dificuldade respiratória severa, sangramentos intensos, perda de consciência.</p>
                    <a href="{{ route('locais') }}" class="btn-action-red">Ver UPAs próximas</a>
                </div>
            </div>

            <div id="statusUpa" class="resultado-box box-upa">
                <div class="resultado-header">
                    <span class="icon-alert"></span>
                    <h2>Recomendação: UPA</h2>
                </div>
                <div class="resultado-body">
                    <p>Seus sintomas indicam necessidade de atendimento de urgência. Procure uma UPA (Unidade de Pronto Atendimento) mais próxima.</p>
                    <p class="atendimento-lista"><strong>UPA atende:</strong> Casos de urgência como fraturas, febres altas persistentes, dores intensas, vômitos persistentes.</p>
                    <a href="{{ route('locais') }}" class="btn-action-orange">Ver UPAs próximas</a>
                </div>
            </div>

            <div id="statusUbs" class="resultado-box box-ubs">
                <div class="resultado-header">
                    <span class="icon-alert"></span>
                    <h2>Recomendação: UBS</h2>
                </div>
                <div class="resultado-body">
                    <p>Seus sintomas podem ser atendidos em uma UBS (Unidade Básica de Saúde). Agende uma consulta.</p>
                    <p class="atendimento-lista"><strong>UBS atende:</strong> Consultas de rotina, acompanhamento, vacinação, exames preventivos, tratamentos contínuos.</p>
                    <a href="{{ route('locais') }}" class="btn-action-green">Ver UBS próximas</a>
                </div>
            </div>

        </div>
    </main>
@endsection

@section('scripts')
    <script src="{{ asset('js/sintomas.js') }}"></script>
@endsection
