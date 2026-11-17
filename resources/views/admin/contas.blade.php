@extends('layouts.app')

@section('title', 'HAWB - Registro de Contas')

@section('content')
    <div class="page-container">
        <div class="section-header">
            <h1><i class="fa-solid fa-clipboard-user" style="color:#009639;"></i> Registro de Contas</h1>
            <p class="subtitle">
                Todas as contas de pacientes e médicos. Bloqueie o chat ou bane uma conta —
                no banimento o motivo é <strong>obrigatório</strong> e será exibido para o usuário.
                Contas de administrador são protegidas.
            </p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="num">{{ $stats['total'] }}</div>
                <div class="lbl">Contas</div>
            </div>
            <div class="stat-card">
                <div class="num">{{ $stats['pacientes'] }}</div>
                <div class="lbl">Pacientes</div>
            </div>
            <div class="stat-card">
                <div class="num">{{ $stats['medicos'] }}</div>
                <div class="lbl">Médicos</div>
            </div>
            <div class="stat-card">
                <div class="num">{{ $stats['bloqueados'] }}</div>
                <div class="lbl">Chat bloqueado</div>
            </div>
            <div class="stat-card">
                <div class="num">{{ $stats['banidos'] }}</div>
                <div class="lbl">Banidas</div>
            </div>
        </div>

        @if($users->isEmpty())
            <div class="empty-state" style="border:1px dashed #d1d5db; border-radius:12px;">
                <i class="fa-solid fa-users-slash"></i>
                <p>Nenhuma conta de paciente ou médico cadastrada.</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Tipo</th>
                            <th>Situação</th>
                            <th>Chat com médicos</th>
                            <th>Conta</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $account)
                            @php
                                $roleLabel = $account->isDoctor()
                                    ? ($account->status === \App\Models\User::STATUS_PENDING ? 'Médico (pendente)' : ($account->status === \App\Models\User::STATUS_REJECTED ? 'Médico (recusado)' : 'Médico'))
                                    : 'Paciente';
                                $statusLabel = [
                                    \App\Models\User::STATUS_PENDING => 'Aguardando aprovação',
                                    \App\Models\User::STATUS_APPROVED => 'Aprovada',
                                    \App\Models\User::STATUS_REJECTED => 'Recusada',
                                ][$account->status] ?? '—';
                                $chatBloqueado = $account->chat_blocked_at !== null;
                                $banida = $account->banned_at !== null;
                            @endphp
                            <tr>
                                <td>{{ $account->name }}</td>
                                <td>{{ $account->email }}</td>
                                <td>{{ $roleLabel }}</td>
                                <td>{{ $statusLabel }}</td>
                                <td>
                                    @if($chatBloqueado)
                                        <span class="status-badge status-off" title="{{ $account->chat_blocked_at->format('d/m/Y H:i') }}">
                                            Bloqueado
                                        </span>
                                    @else
                                        <span class="status-badge status-on">Liberado</span>
                                    @endif
                                </td>
                                <td>
                                    @if($banida)
                                        <span class="status-badge status-ban" title="{{ $account->banned_at->format('d/m/Y H:i') }}">
                                            Banida
                                        </span>
                                    @else
                                        <span class="status-badge status-on">Ativa</span>
                                    @endif
                                </td>
                                <td class="actions-cell">
                                    {{-- Bloquear / desbloquear chat --}}
                                    @if($chatBloqueado)
                                        <form action="{{ route('admin.unblockChat', $account) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn-primary btn-sm">Desbloquear chat</button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.blockChat', $account) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn-secondary btn-sm">Bloquear chat</button>
                                        </form>
                                    @endif

                                    {{-- Banir (abre modal com motivo obrigatório) / reativar --}}
                                    @if($banida)
                                        <form action="{{ route('admin.unban', $account) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn-primary btn-sm">Reativar conta</button>
                                        </form>
                                    @else
                                        <button type="button" class="btn-danger btn-sm"
                                                data-ban-id="{{ $account->id }}"
                                                data-ban-name="{{ $account->name }}">
                                            Banir
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <p class="accounts-note">
            <i class="fa-solid fa-circle-info"></i>
            Conta bloqueada no chat não acessa o feed nem as conversas com médicos até o desbloqueio.
            Conta banida é deslogada na hora e vê a tela com o motivo escrito aqui.
        </p>
    </div>

    {{-- ===== Modal de banimento (motivo obrigatório) ===== --}}
    <div class="ban-overlay" id="banOverlay" hidden>
        <div class="ban-modal" role="dialog" aria-modal="true" aria-labelledby="banTitle">
            <h2 id="banTitle"><i class="fa-solid fa-ban" style="color:#dc2626;"></i> Banir conta</h2>
            <p>
                Você está banindo <strong id="banUserName">—</strong>.
                O motivo é <strong>obrigatório</strong>: o usuário será deslogado na hora e verá
                exatamente o texto abaixo na tela de erro grave.
            </p>

            <form method="POST" action="" id="banForm">
                @csrf
                <label for="banMotivo">Motivo do banimento</label>
                <textarea id="banMotivo" name="motivo" rows="4" required minlength="5" maxlength="1000"
                          placeholder="Ex.: Publicou conteúdo ofensivo no feed ou violou as regras da comunidade."></textarea>
                <p class="ban-hint">Mínimo de 5 caracteres — este texto será exibido para o usuário.</p>

                <div class="ban-actions">
                    <button type="button" class="btn-secondary" id="banCancel">Cancelar</button>
                    <button type="submit" class="btn-danger">Confirmar banimento</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        (function () {
            var overlay = document.getElementById('banOverlay');
            var form = document.getElementById('banForm');
            var nameEl = document.getElementById('banUserName');
            var motivo = document.getElementById('banMotivo');

            function open(id, name) {
                form.action = '{{ url('/admin/contas') }}/' + id + '/banir';
                nameEl.textContent = name;
                motivo.value = '';
                overlay.hidden = false;
                motivo.focus();
            }

            function close() {
                overlay.hidden = true;
            }

            document.querySelectorAll('[data-ban-id]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    open(btn.dataset.banId, btn.dataset.banName);
                });
            });

            document.getElementById('banCancel').addEventListener('click', close);
            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) close();
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !overlay.hidden) close();
            });
        })();
    </script>
@endsection
