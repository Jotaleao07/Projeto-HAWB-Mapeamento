@extends('layouts.app')

@section('title', 'HAWB - Nossa Equipe')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/equipe.css') }}">
@endsection

@section('content')
    <main class="equipe-container">

        <section class="section-header">
            <div class="title-with-icon">
                <span class="main-icon">👥</span>
                <h1>Nossa Equipe</h1>
            </div>
            <p class="subtitle">Conheça os desenvolvedores e idealizadores por trás da plataforma HAWB</p>
        </section>

        @if($members->isEmpty())
            <div class="empty-state">
                <i class="fa-solid fa-users"></i>
                <p>Nenhum integrante cadastrado até o momento.</p>
            </div>
        @else
            <section class="equipe-grid">
                @foreach($members as $member)
                    <div class="membro-card" data-member="{{ $member->id }}">
                        <div class="avatar-box">
                            <img src="{{ $member->photo ? asset('img/'.$member->photo) : asset('img/Professora.png') }}"
                                 alt="Foto de {{ $member->name }}">
                        </div>
                        <h3>{{ $member->name }}</h3>
                        <p class="membro-cargo">{{ $member->role }}</p>
                        <button class="btn-more-info" type="button">Mais informações</button>
                    </div>
                @endforeach
            </section>

            <section id="detalhes-membro-container" class="detalhes-membro-container" style="display: none;">
                <div class="detalhes-header">
                    <span class="badge-status">Perfil Ativo</span>
                    <h2 id="detalhe-nome">Nome do Integrante</h2>
                    <p id="detalhe-cargo" class="detalhe-cargo-sub">Cargo correspondente</p>
                </div>

                <div class="detalhes-body">
                    <div class="detalhes-section">
                        <h4>Sobre mim</h4>
                        <p id="detalhe-bio">Biografia detalhada do integrante...</p>
                    </div>

                    <div class="detalhes-section">
                        <h4>Principais Tecnologias e Hard Skills:</h4>
                        <div id="detalhe-tags" class="skills-tags"></div>
                    </div>

                    <div class="detalhes-section links-section">
                        <h4>Redes Profissionais:</h4>
                        <div class="social-buttons">
                            <a id="link-linkedin" href="#" target="_blank" rel="noopener" class="btn-social btn-linkedin">
                                <i class="fab fa-linkedin"></i> LinkedIn
                            </a>
                            <a id="link-github" href="#" target="_blank" rel="noopener" class="btn-social btn-github">
                                <i class="fab fa-github"></i> GitHub
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        @endif

    </main>
@endsection

@push('scripts')
<script>
// Base vinda do banco de dados (team_members) — sem dados fixos neste arquivo.
const baseDadosEquipe = @json($membersJs);
</script>
<script src="{{ asset('js/equipe.js') }}"></script>
@endpush
