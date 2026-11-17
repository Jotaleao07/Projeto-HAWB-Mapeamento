@extends('layouts.app')

@section('title', 'HAWB - Locais de Atendimento')
@section('styles')
    {{-- v= filemtime: muda a URL a cada deploy, o navegador nunca usa CSS antigo em cache --}}
    <link rel="stylesheet" href="{{ asset('css/locais.css') }}?v={{ @filemtime(public_path('css/locais.css')) }}">
@endsection

@section('content')
    <main class="locais-container">
        <h1>Locais de Atendimento</h1>
        <p class="subtitle">Pesquise sua cidade e encontre as UPAs e UBS com serviços disponíveis</p>

        {{-- ===== Passo 1: busca de cidade com autocompletar ===== --}}
        <section class="city-search">
            <label for="cityInput">Pesquisar Cidade</label>
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                <input type="text" id="cityInput" placeholder="Digite o nome da cidade..."
                       autocomplete="off" role="combobox" aria-expanded="false"
                       aria-controls="citySuggestions" aria-autocomplete="list"
                       aria-label="Pesquisar cidade da Bahia">
                <ul id="citySuggestions" class="city-suggestions" role="listbox" hidden></ul>
            </div>
            <p class="search-hint">Digite ao menos 2 letras — mostramos as cidades da Bahia.</p>
        </section>

        {{-- ===== Passo 2: cidade escolhida (estado + distância + tipo) ===== --}}
        <section id="cityPanel" class="city-panel" hidden>
            <div class="city-panel-head">
                <span class="city-pin" aria-hidden="true"><i class="fa-solid fa-location-dot"></i></span>
                <div class="city-panel-info">
                    <h2 id="cityName">—</h2>
                    <p class="city-meta">
                        <span id="cityState">Bahia — BA</span>
                        <span class="city-dot" aria-hidden="true">•</span>
                        <span id="cityDistance">calculando distância…</span>
                    </p>
                </div>
                <button type="button" class="btn-link" id="changeCity">Trocar cidade</button>
            </div>

            <div id="typeChoice" class="type-choice" hidden>
                <p class="type-choice-label">Escolha o tipo de unidade:</p>
                <div class="filter-buttons" id="typeButtons">
                    <button class="btn-filter" data-type="upa" type="button">
                        UPA <span class="type-count" id="upaCount"></span>
                    </button>
                    <button class="btn-filter" data-type="ubs" type="button">
                        UBS <span class="type-count" id="ubsCount"></span>
                    </button>
                </div>
            </div>

            <div id="noDataMsg" class="empty-state" hidden>
                <i class="fa-solid fa-circle-info"></i>
                <p>Ainda não temos dados de UPAs e UBS para esta cidade — em breve.</p>
            </div>
        </section>

        {{-- ===== Passo 3: lista de unidades do tipo escolhido ===== --}}
        <section id="unitsPanel" hidden>
            <div class="step-head">
                <h2 id="unitsTitle">—</h2>
                <button type="button" class="btn-link" id="backToTypes">← Trocar tipo</button>
            </div>
            <div class="locais-grid" id="unitsGrid"></div>
        </section>

        {{-- ===== Passo 4: detalhe da unidade (mapa + informações) ===== --}}
        <section id="detailPanel" class="detail-panel" hidden>
            <div class="step-head">
                <button type="button" class="btn-link" id="backToUnits">← Voltar para a lista</button>
            </div>

            <div class="detail-card">
                <div class="detail-map">
                    <iframe id="mapFrame" title="Mapa da unidade de saúde" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                </div>

                <div class="detail-body">
                    <div class="card-header-tags">
                        <span class="tag-badge" id="detailBadge">—</span>
                        <span class="tag-city" id="detailCity">—</span>
                    </div>
                    <h2 id="detailName">—</h2>

                    <div class="info-line">
                        <span class="icon" aria-hidden="true">📍</span>
                        <p id="detailAddress">—</p>
                    </div>
                    <div class="info-line">
                        <span class="icon" aria-hidden="true">📞</span>
                        <p id="detailPhone">—</p>
                    </div>
                    <div class="info-line">
                        <span class="icon" aria-hidden="true">🕒</span>
                        <p id="detailHours">—</p>
                    </div>

                    <div class="servicos-section">
                        <p class="servicos-title">Serviços disponíveis:</p>
                        <div class="servicos-tags" id="detailServices"></div>
                    </div>

                    <a id="directionsLink" class="btn-google-maps" href="#" target="_blank" rel="noopener">
                        <i class="fa-solid fa-diamond-turn-right" aria-hidden="true"></i> Como chegar
                    </a>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    <script>
        // Base de dados do projeto (vinda do controller)
        window.HAWB_CITIES = @json($cities);
        window.HAWB_UNITS_BY_CITY = @json($unitsByCity);
        window.HAWB_CITY_COORDS = @json($cityCoords);
    </script>
    <script src="{{ asset('js/locais.js') }}?v={{ @filemtime(public_path('js/locais.js')) }}"></script>
@endsection
