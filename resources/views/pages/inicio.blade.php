@extends('layouts.app')

@section('title', 'HAWB - O melhor mapeamento de saúde')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
@endsection

@section('content')
    <section class="hero-section">
        <div class="hero-text">
            <h1>Sua saúde vira nossa obrigação também!</h1>
            <p>Encontre rapidamente o atendimento médico adequado para você. Localize UPAs, UBS e especialistas na sua região.</p>
            <button class="btn-cta" id="btnComecar">Começar Agora &rarr;</button>
        </div>
        <div class="hero-image-box">
            <img src="{{ asset('img/Captura de tela 2026-05-14 074341.png') }}" alt="Médica apontando para o texto" class="doctor-img-female">
        </div>
    </section>

    <section class="about-section">
        <div class="about-image-box">
            <div class="stamp-logo"></div>
            <img src="{{ asset('img/Captura de tela 2026-05-14 074126.png') }}" alt="Médico sorrindo com prancheta" class="doctor-img-male">
        </div>
        <div class="about-text">
            <h2>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#009639" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                Sobre o HAWB
            </h2>
            <p>Somos uma plataforma comprometida em facilitar o acesso à saúde pública, conectando pacientes aos serviços médicos adequados de forma rápida e eficiente.</p>
            <p class="sub-text">Desenvolvido com dedicação para servir a comunidade da Bahia, especialmente nas cidades de Salvador, Camaçari, Lauro de Freitas e Dias d'Ávila.</p>
        </div>
    </section>

    <section class="mission-section">
        <h2>Nossa Missão</h2>
        <p>Democratizar o acesso à informação sobre saúde pública, oferecendo uma ferramenta intuitiva que orienta os cidadãos sobre onde buscar atendimento médico, especialistas disponíveis e como agendar consultas na região metropolitana de Salvador.</p>
    </section>

    <section class="quote-container">
        <div class="quote-left">
            <div class="title-with-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#009639" stroke-width="2">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
                <h3>Saúde para todos</h3>
            </div>
            <p class="italic-quote">"Cuidar da saúde é um direito de todos e um compromisso nosso."</p>
            <p class="small-desc">Acreditamos que o acesso à informação de qualidade sobre saúde pública transforma vidas e fortalece comunidades.</p>
        </div>
        <div class="quote-right-card">
            <div class="card-overlay">
                <div class="card-badge">HAWB</div>
                <img src="{{ asset('img/Captura de tela 2026-05-14 081829.png') }}" alt="">
            </div>
        </div>
    </section>

    <section class="services-section">
        <h2>Nossos Serviços</h2>
        <p class="services-subtitle">Tudo que você precisa para cuidar da sua saúde em um só lugar</p>

        <div class="services-grid">
            <a class="service-card" href="{{ route('sintomas') }}">
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#009639" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                </div>
                <h3>Análise de Sintomas</h3>
                <p class="btn-action-orange">Identifique onde procurar atendimento baseado nos seus sintomas</p>
            </a>

            <a class="service-card" href="{{ route('locais') }}">
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#009639" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                </div>
                <h3>Encontre Locais</h3>
                <p class="btn-action-orange">Veja UPAs e UBS próximas a você com serviços disponíveis</p>
            </a>

            <a class="service-card" href="{{ route('tipos') }}">
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#009639" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
                <h3>Especialidades</h3>
                <p class="btn-action-orange">Encontre médicos especialidades e saiba como agendar</p>
            </a>

            <a class="service-card" href="{{ route('chat.index') }}">
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#009639" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                </div>
                <h3>Chat com Médicos</h3>
                <p class="btn-action-orange">Leia orientações publicadas e converse direto com um médico</p>
            </a>

            <a class="service-card" href="{{ route('contact.show') }}">
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#009639" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                </div>
                <h3>Fale Conosco</h3>
                <p class="btn-action-orange">Envie sua dúvida, sugestão ou crítica pelo formulário de contato</p>
            </a>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('js/inicio.js') }}"></script>
@endsection
