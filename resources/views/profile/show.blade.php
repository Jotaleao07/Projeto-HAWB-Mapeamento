@extends('layouts.app')

@section('title', 'HAWB - Meu Perfil')

@section('content')
    <div class="page-container" style="max-width: 900px;">
        <div class="section-header">
            <h1>Meu Perfil</h1>
            <p class="subtitle">Seus dados, suas funcionalidades e configurações de acessibilidade.</p>
        </div>

        {{-- ---------- Dados da conta ---------- --}}
        <div class="form-card profile-card">
            <div class="profile-avatar" aria-hidden="true">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            <h2>{{ $user->name }}</h2>
            <p class="profile-email">{{ $user->email }}</p>

            @php
                $roleLabel = $user->isAdmin() ? 'Administrador' : ($user->isDoctor() ? 'Médico' : ($user->isPendingDoctor() ? 'Médico (aguardando aprovação)' : 'Paciente'));
                $roleClass = $user->isAdmin() ? 'role-admin' : ($user->isDoctor() ? 'role-doctor' : 'role-patient');
            @endphp
            <span class="badge-role {{ $roleClass }}">{{ $roleLabel }}</span>

            <form method="POST" action="{{ route('profile.name') }}" class="profile-name-form">
                @csrf
                <div class="form-group" style="margin: 22px 0 12px; text-align: left;">
                    <label for="profile-name-input">Nome exibido</label>
                    <input type="text" id="profile-name-input" name="name" value="{{ old('name', $user->name) }}"
                           required maxlength="255">
                </div>
                <button type="submit" class="btn-primary">Salvar nome</button>
            </form>
        </div>

        {{-- ---------- Funcionalidades exclusivas do papel ---------- --}}
        <div class="profile-section">
            <h2><i class="fa-solid fa-puzzle-piece"></i> Suas funcionalidades</h2>
            <div class="profile-actions">
                <a href="{{ route('chat.conversations') }}" class="profile-action">
                    <i class="fa-solid fa-comments" aria-hidden="true"></i>
                    <strong>Minhas conversas</strong>
                    <small>Acompanhe seus atendimentos em andamento</small>
                </a>

                @if($user->isDoctor())
                    <a href="{{ route('doctor.chat.create') }}" class="profile-action">
                        <i class="fa-solid fa-pen" aria-hidden="true"></i>
                        <strong>Publicar</strong>
                        <small>Crie uma nova publicação médica para o feed</small>
                    </a>
                @endif

                @if($user->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="profile-action">
                        <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
                        <strong>Painel Administrativo</strong>
                        <small>Aprove contas médicas e acompanhe os contatos</small>
                    </a>
                @endif
            </div>
        </div>

        {{-- ---------- Configurações / Acessibilidade ---------- --}}
        <div class="profile-section" id="configuracoes">
            <div class="a11y-section-head">
                <h2><i class="fa-solid fa-universal-access"></i> Configurações</h2>
                <span class="a11y-saved" id="a11ySaved" role="status" aria-live="polite"></span>
            </div>
            <p class="subtitle" style="margin-bottom: 18px;">
                Ative até <strong>4 grupos de acessibilidade</strong>. As alterações são salvas automaticamente
                e valem em todas as páginas do site.
            </p>

            <div class="a11y-groups">

                {{-- ===== 1. VISUAL ===== --}}
                <div class="a11y-group {{ $preferences['visual']['enabled'] ? 'is-on' : '' }}" data-group="visual">
                    <div class="a11y-group-head">
                        <span class="a11y-group-icon"><i class="fa-solid fa-eye" aria-hidden="true"></i></span>
                        <span class="a11y-group-title">
                            <strong>Acessibilidade Visual</strong>
                            <small>Para baixa visão e daltonismo</small>
                        </span>
                        <button type="button" class="a11y-switch" role="switch"
                                aria-checked="{{ $preferences['visual']['enabled'] ? 'true' : 'false' }}"
                                data-group="visual" data-key="enabled"
                                aria-label="Ativar acessibilidade visual"><span class="knob"></span></button>
                    </div>
                    <div class="a11y-group-body">
                        @include('profile.switch', ['group' => 'visual', 'key' => 'contrast', 'checked' => $preferences['visual']['contrast'],
                            'label' => 'Alto contraste', 'desc' => 'Textos mais escuros, bordas firmes e links em destaque'])
                        @include('profile.switch', ['group' => 'visual', 'key' => 'bigfont', 'checked' => $preferences['visual']['bigfont'],
                            'label' => 'Aumentar tamanho do texto', 'desc' => 'Amplia todo o conteúdo do site em 15%'])

                        <div class="a11y-option">
                            <span class="a11y-option-text">
                                <strong>Daltonismo</strong>
                                <small>Ajusta as cores para o tipo de deficiência colorimétrica</small>
                            </span>
                            <select id="daltonism-select" class="a11y-select" aria-label="Tipo de daltonismo">
                                <option value="off" {{ $preferences['visual']['daltonism'] === 'off' ? 'selected' : '' }}>Desligado</option>
                                <option value="protanopia" {{ $preferences['visual']['daltonism'] === 'protanopia' ? 'selected' : '' }}>Protanopia (vermelho)</option>
                                <option value="deuteranopia" {{ $preferences['visual']['daltonism'] === 'deuteranopia' ? 'selected' : '' }}>Deuteranopia (verde)</option>
                                <option value="tritanopia" {{ $preferences['visual']['daltonism'] === 'tritanopia' ? 'selected' : '' }}>Tritanopia (azul)</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- ===== 2. AUDITIVA ===== --}}
                <div class="a11y-group {{ $preferences['auditiva']['enabled'] ? 'is-on' : '' }}" data-group="auditiva">
                    <div class="a11y-group-head">
                        <span class="a11y-group-icon"><i class="fa-solid fa-deaf" aria-hidden="true"></i></span>
                        <span class="a11y-group-title">
                            <strong>Acessibilidade Auditiva</strong>
                            <small>Para pessoas surdas ou com baixa audição</small>
                        </span>
                        <button type="button" class="a11y-switch" role="switch"
                                aria-checked="{{ $preferences['auditiva']['enabled'] ? 'true' : 'false' }}"
                                data-group="auditiva" data-key="enabled"
                                aria-label="Ativar acessibilidade auditiva"><span class="knob"></span></button>
                    </div>
                    <div class="a11y-group-body">
                        @include('profile.switch', ['group' => 'auditiva', 'key' => 'visual_alerts', 'checked' => $preferences['auditiva']['visual_alerts'],
                            'label' => 'Alertas visuais (flash)', 'desc' => 'Um relâmpago de luz na tela quando chega mensagem nova'])
                        @include('profile.switch', ['group' => 'auditiva', 'key' => 'vibrate', 'checked' => $preferences['auditiva']['vibrate'],
                            'label' => 'Vibração ao receber mensagem', 'desc' => 'O celular vibra quando chega uma mensagem no chat'])
                        @include('profile.switch', ['group' => 'auditiva', 'key' => 'text_alerts', 'checked' => $preferences['auditiva']['text_alerts'],
                            'label' => 'Alertas em texto', 'desc' => 'Aviso na tela escrevendo o que aconteceu (ex.: "Nova mensagem recebida")'])
                    </div>
                </div>

                {{-- ===== 3. MOTORA ===== --}}
                <div class="a11y-group {{ $preferences['motora']['enabled'] ? 'is-on' : '' }}" data-group="motora">
                    <div class="a11y-group-head">
                        <span class="a11y-group-icon"><i class="fa-solid fa-hand-pointer" aria-hidden="true"></i></span>
                        <span class="a11y-group-title">
                            <strong>Acessibilidade Motora</strong>
                            <small>Para dificuldade de movimento e clique</small>
                        </span>
                        <button type="button" class="a11y-switch" role="switch"
                                aria-checked="{{ $preferences['motora']['enabled'] ? 'true' : 'false' }}"
                                data-group="motora" data-key="enabled"
                                aria-label="Ativar acessibilidade motora"><span class="knob"></span></button>
                    </div>
                    <div class="a11y-group-body">
                        @include('profile.switch', ['group' => 'motora', 'key' => 'keyboard', 'checked' => $preferences['motora']['keyboard'],
                            'label' => 'Navegação por teclado', 'desc' => 'Destaque forte no elemento focado para pular de item em item com Tab'])
                        @include('profile.switch', ['group' => 'motora', 'key' => 'bigclick', 'checked' => $preferences['motora']['bigclick'],
                            'label' => 'Alvos de clique maiores', 'desc' => 'Botões e links com área de toque maior e mais espaçados'])
                        @include('profile.switch', ['group' => 'motora', 'key' => 'safe_click', 'checked' => $preferences['motora']['safe_click'],
                            'label' => 'Proteção contra clique acidental', 'desc' => 'Após clicar, o item fica travado por 1 segundo (evita clique duplo)'])
                    </div>
                </div>

                {{-- ===== 4. COGNITIVA ===== --}}
                <div class="a11y-group {{ $preferences['cognitiva']['enabled'] ? 'is-on' : '' }}" data-group="cognitiva">
                    <div class="a11y-group-head">
                        <span class="a11y-group-icon"><i class="fa-solid fa-brain" aria-hidden="true"></i></span>
                        <span class="a11y-group-title">
                            <strong>Acessibilidade Cognitiva</strong>
                            <small>Para atenção, leitura e memória</small>
                        </span>
                        <button type="button" class="a11y-switch" role="switch"
                                aria-checked="{{ $preferences['cognitiva']['enabled'] ? 'true' : 'false' }}"
                                data-group="cognitiva" data-key="enabled"
                                aria-label="Ativar acessibilidade cognitiva"><span class="knob"></span></button>
                    </div>
                    <div class="a11y-group-body">
                        @include('profile.switch', ['group' => 'cognitiva', 'key' => 'no_animations', 'checked' => $preferences['cognitiva']['no_animations'],
                            'label' => 'Reduzir animações', 'desc' => 'Desliga transições, efeitos de hover e movimentos da página'])
                        @include('profile.switch', ['group' => 'cognitiva', 'key' => 'underline_links', 'checked' => $preferences['cognitiva']['underline_links'],
                            'label' => 'Sublinhar todos os links', 'desc' => 'Deixa claro o que é clicável'])
                        @include('profile.switch', ['group' => 'cognitiva', 'key' => 'reading_mode', 'checked' => $preferences['cognitiva']['reading_mode'],
                            'label' => 'Modo de leitura', 'desc' => 'Coluna mais estreita, linhas mais espaçadas e foco no conteúdo'])
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    var route = @json(route('profile.settings'));
    var timer = null;
    var saved = document.getElementById('a11ySaved');

    function showSaved() {
        if (!saved) return;
        saved.textContent = '✓ Salvo';
        saved.classList.add('visible');
        clearTimeout(saved._t);
        saved._t = setTimeout(function () { saved.classList.remove('visible'); }, 2000);
    }

    function collect() {
        var prefs = {};
        document.querySelectorAll('.a11y-switch').forEach(function (sw) {
            var g = sw.dataset.group, k = sw.dataset.key;
            prefs[g] = prefs[g] || {};
            prefs[g][k] = sw.getAttribute('aria-checked') === 'true';
        });
        var sel = document.getElementById('daltonism-select');
        if (sel) { prefs.visual = prefs.visual || {}; prefs.visual.daltonism = sel.value; }
        return prefs;
    }

    function save() {
        var token = document.querySelector('meta[name="csrf-token"]').content;
        fetch(route, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ preferences: collect() })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data && typeof data.class === 'string') {
                var cls = document.documentElement.className.replace(/\ba11y-[\w-]+/g, '').replace(/\s+/g, ' ').trim();
                document.documentElement.className = (cls + ' ' + data.class).trim();
            }
            showSaved();
        })
        .catch(function () {});
    }

    function scheduleSave() {
        clearTimeout(timer);
        timer = setTimeout(save, 350);
    }

    function syncGroup(g) {
        var head = document.querySelector('.a11y-group[data-group="' + g + '"] .a11y-switch[data-key="enabled"]');
        if (!head) return;
        var on = head.getAttribute('aria-checked') === 'true';
        var group = head.closest('.a11y-group');
        group.classList.toggle('is-on', on);
        var body = group.querySelector('.a11y-group-body');
        if (body) body.classList.toggle('disabled', !on);
    }

    document.querySelectorAll('.a11y-switch').forEach(function (sw) {
        syncGroup(sw.dataset.group);
        sw.addEventListener('click', function () {
            var on = sw.getAttribute('aria-checked') !== 'true';
            sw.setAttribute('aria-checked', on ? 'true' : 'false');
            if (sw.dataset.key === 'enabled') syncGroup(sw.dataset.group);
            scheduleSave();
        });
    });

    var sel = document.getElementById('daltonism-select');
    if (sel) sel.addEventListener('change', scheduleSave);
})();
</script>
@endpush
