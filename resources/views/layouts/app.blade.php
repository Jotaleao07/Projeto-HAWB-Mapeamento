<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HAWB - O melhor mapeamento de saúde')</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/devicons/devicon@v2.15.1/devicon.min.css">

    {{-- v= filemtime: muda a URL a cada deploy, o navegador nunca usa CSS antigo em cache --}}
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}?v={{ @filemtime(public_path('css/layout.css')) }}">
    @yield('styles')
    @stack('head')
</head>
<body>

<div class="disclaimer" id="disclaimer">
    <strong>Aviso:</strong> As publicações médicas têm caráter informativo e não substituem uma consulta profissional.
    Em caso de emergência, procure atendimento imediato ou ligue 192.
</div>

<header class="navbar">
    <div class="logo-container">
        <i class="fa-solid fa-heart-pulse logo-icon"></i>
        <div>
            <h1 class="logo-title">HAWB</h1>
            <p class="logo-subtitle">O melhor mapeamento de saúde</p>
        </div>
    </div>

    @auth
        <button class="nav-toggle" id="navToggle" type="button" aria-label="Abrir menu" aria-expanded="false" aria-controls="navLinks">
            <i class="fa-solid fa-bars"></i>
        </button>
    @endauth

    <nav class="nav-links {{ auth()->check() ? '' : 'is-public' }}" id="navLinks">
        @auth
            <a href="{{ route('inicio') }}" class="{{ request()->routeIs('inicio') ? 'active' : '' }}">Início</a>
            <a href="{{ route('sintomas') }}" class="{{ request()->routeIs('sintomas') ? 'active' : '' }}">Sintomas</a>
            <a href="{{ route('locais') }}" class="{{ request()->routeIs('locais') ? 'active' : '' }}">Locais</a>
            <a href="{{ route('tipos') }}" class="{{ request()->routeIs('tipos') ? 'active' : '' }}">Tipos</a>
            <a href="{{ route('equipe') }}" class="{{ request()->routeIs('equipe') ? 'active' : '' }}">Equipe</a>
            <a href="{{ route('chat.index') }}" class="{{ request()->routeIs('chat.*') ? 'active' : '' }}">Chat com Médicos</a>
            <a href="{{ route('contact.show') }}" class="{{ request()->routeIs('contact.*') ? 'active' : '' }}">Contato</a>
        @endauth

        <div class="auth-nav">
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}">Admin</a>
                @endif
                @if(auth()->user()->isDoctor() || auth()->user()->isPendingDoctor())
                    <a href="{{ route('doctor.chat.create') }}">Publicar</a>
                @endif
                <div class="user-menu" id="userMenu">
                    <button type="button" class="user-chip user-menu-toggle" id="userMenuToggle"
                            aria-haspopup="true" aria-expanded="false" aria-controls="userMenuDropdown"
                            title="{{ auth()->user()->email }}">
                        <i class="fa-solid fa-user"></i> <span class="user-chip-name">{{ auth()->user()->name }}</span>
                        <i class="fa-solid fa-chevron-down user-chip-caret" aria-hidden="true"></i>
                    </button>
                    <div class="user-menu-dropdown" id="userMenuDropdown" role="menu">
                        <a href="{{ route('chat.conversations') }}" role="menuitem">
                            <i class="fa-solid fa-comments" aria-hidden="true"></i> Minhas conversas
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" role="menuitem">
                                <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i> Sair
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">Entrar</a>
                <a href="{{ route('register') }}" class="btn-nav {{ request()->routeIs('register') ? 'active' : '' }}">Cadastrar</a>
            @endauth
        </div>
    </nav>
</header>

<main class="page-main">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin:0;padding-left:20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<footer class="footer">
    <div class="footer-top">
        <div class="footer-logo">
            <i class="fa-solid fa-heart-pulse"></i>
            <div>
                <h4>HAWB | Health and Well-being</h4>
                <p>O melhor mapeamento de saúde</p>
            </div>
        </div>
        <div class="footer-info">
            <span><i class="fa-solid fa-location-dot"></i> Camaçari/BA</span>
            <span><i class="fa-solid fa-envelope"></i> hawb.med@gmail.com</span>
        </div>
    </div>
    <hr class="footer-divider">
    <div class="footer-bottom">
        <p>&copy; 2026 - HAWB | Health and Well-being</p>
        <p class="footer-slogan">Mais que produção, uma relação com você!</p>
    </div>
</footer>

@yield('scripts')
@stack('scripts')

<script>
// Aviso de emergência: some com fade 1 minuto após abrir a página
(function () {
    var aviso = document.getElementById('disclaimer');
    if (!aviso) return;
    setTimeout(function () { aviso.classList.add('hide'); }, 60000);
})();

// Menu do usuário: clicar no nome abre Minhas conversas / Sair
(function () {
    var menu = document.getElementById('userMenu');
    var toggle = document.getElementById('userMenuToggle');
    if (!menu || !toggle) return;

    toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        var open = menu.classList.toggle('open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    // Fecha ao clicar fora ou ao pressionar Esc
    document.addEventListener('click', function (e) {
        if (!menu.contains(e.target)) {
            menu.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            menu.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
})();

// Menu responsivo (hambúrguer)
(function () {
    var toggle = document.getElementById('navToggle');
    var links = document.getElementById('navLinks');
    if (!toggle || !links) return;

    toggle.addEventListener('click', function (e) {
        e.stopPropagation(); // impede que o clique no botão seja tratado como "clique fora"
        var open = links.classList.toggle('open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        toggle.innerHTML = open
            ? '<i class="fa-solid fa-xmark"></i>'
            : '<i class="fa-solid fa-bars"></i>';
    });

    // Fecha o menu ao clicar em um link ou fora dele
    links.addEventListener('click', function (e) {
        if (e.target.closest('a')) {
            links.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.innerHTML = '<i class="fa-solid fa-bars"></i>';
        }
    });
    document.addEventListener('click', function (e) {
        if (!links.contains(e.target) && !toggle.contains(e.target)) {
            links.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.innerHTML = '<i class="fa-solid fa-bars"></i>';
        }
    });
})();
</script>
</body>
</html>
