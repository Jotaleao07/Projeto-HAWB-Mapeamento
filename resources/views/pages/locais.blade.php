@extends('layouts.app')

@section('title', 'HAWB - Locais de Atendimento')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/locais.css') }}">
@endsection

@section('content')
    <main class="locais-container">
        <h1>Locais de Atendimento</h1>
        <p class="subtitle">Encontre UPAs e UBS próximas a você</p>

        <section class="filter-section">
            <div class="filter-group">
                <label>Tipo de unidade:</label>
                <div class="filter-buttons" id="filter-type">
                    <button class="btn-filter active" data-type="todos">Todos</button>
                    <button class="btn-filter" data-type="upa">UPA</button>
                    <button class="btn-filter" data-type="ubs">UBS</button>
                </div>
            </div>

            <div class="filter-group">
                <label>Cidade:</label>
                <div class="filter-buttons" id="filter-city">
                    <button class="btn-filter active" data-city="todos">Todas</button>
                    <button class="btn-filter" data-city="salvador">Salvador</button>
                    <button class="btn-filter" data-city="camacari">Camaçari</button>
                    <button class="btn-filter" data-city="lauro">Lauro de Freitas</button>
                    <button class="btn-filter" data-city="dias">Dias d'Ávila</button>
                </div>
            </div>
        </section>

        <section class="locais-grid" id="locais-grid">
            @foreach($locations as $unit)
                <div class="local-card" data-type="{{ $unit['type'] }}" data-city="{{ $unit['city'] }}" id="card-{{ $unit['id'] }}">
                    <div class="card-header-tags">
                        <span class="tag-badge badge-{{ $unit['type'] }}">{{ strtoupper($unit['type']) }}</span>
                        <span class="tag-city">{{ $unit['cityLabel'] }}</span>
                    </div>
                    <h2>{{ $unit['name'] }}</h2>
                    <div class="info-line">
                        <span class="icon">📍</span>
                        <p>{{ $unit['address'] }}</p>
                    </div>
                    <div class="info-line">
                        <span class="icon">📞</span>
                        <p>{{ $unit['phone'] }}</p>
                    </div>
                    <div class="info-line">
                        <span class="icon">🕒</span>
                        <p>{{ $unit['hours'] }}</p>
                    </div>
                    <div class="servicos-section">
                        <p class="servicos-title">Serviços disponíveis:</p>
                        <div class="servicos-tags">
                            @foreach($unit['services'] as $service)
                                <span>{{ $service }}</span>
                            @endforeach
                        </div>
                    </div>
                    <button class="btn-toggle-map" onclick="toggleMap('{{ $unit['id'] }}')">
                        <span class="map-icon"></span> <span class="btn-text">Como Chegar</span>
                    </button>
                    <div class="map-wrapper" id="map-{{ $unit['id'] }}">
                        <div class="map-box">
                            <div class="mock-map">Roteamento para {{ $unit['name'] }}</div>
                            <a href="https://maps.google.com/?q={{ urlencode($unit['name'].' '.$unit['address']) }}"
                               target="_blank" rel="noopener" class="btn-google-maps">Abrir no Google Maps</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </section>
    </main>
@endsection

@section('scripts')
    <script src="{{ asset('js/locais.js') }}"></script>
@endsection
