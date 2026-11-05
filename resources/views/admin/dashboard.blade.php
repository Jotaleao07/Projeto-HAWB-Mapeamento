@extends('layouts.app')

@section('title', 'HAWB - Painel Administrativo')

@section('content')
    <div class="page-container">
        <div class="section-header">
            <h1>Painel Administrativo</h1>
            <p class="subtitle">Aprovação de contas médicas e mensagens recebidas pelo formulário de contato.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="num">{{ $stats['users'] }}</div>
                <div class="lbl">Usuários totais</div>
            </div>
            <div class="stat-card">
                <div class="num">{{ $stats['doctors'] }}</div>
                <div class="lbl">Médicos aprovados</div>
            </div>
            <div class="stat-card">
                <div class="num">{{ $stats['pending'] }}</div>
                <div class="lbl">Aguardando aprovação</div>
            </div>
            <div class="stat-card">
                <div class="num">{{ $stats['contacts'] }}</div>
                <div class="lbl">Contatos recebidos</div>
            </div>
        </div>

        <section style="margin-bottom: 44px;">
            <h2 style="font-size: 1.3rem; color:#111827; margin-bottom: 14px;">
                <i class="fa-solid fa-user-doctor" style="color:#009639;"></i> Contas médicas pendentes
            </h2>

            @if($pendingDoctors->isEmpty())
                <div class="empty-state" style="border:1px dashed #d1d5db; border-radius:12px;">
                    <i class="fa-solid fa-check-circle" style="color:#009639;"></i>
                    <p>Nenhuma conta aguardando aprovação.</p>
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Especialidade</th>
                                <th>CRM</th>
                                <th>Solicitado em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingDoctors as $doctor)
                                <tr>
                                    <td>{{ $doctor->name }}</td>
                                    <td>{{ $doctor->email }}</td>
                                    <td>{{ $doctor->specialty ?? '—' }}</td>
                                    <td>{{ $doctor->crm ?? '—' }}</td>
                                    <td>{{ $doctor->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <form action="{{ route('admin.approve', $doctor) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn-primary" style="padding:7px 14px; font-size:.83rem;">
                                                Aprovar
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.reject', $doctor) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn-danger">Recusar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section>
            <h2 style="font-size: 1.3rem; color:#111827; margin-bottom: 14px;">
                <i class="fa-solid fa-envelope" style="color:#009639;"></i> Últimos contatos recebidos
            </h2>

            @if($contacts->isEmpty())
                <div class="empty-state" style="border:1px dashed #d1d5db; border-radius:12px;">
                    <i class="fa-solid fa-inbox"></i>
                    <p>Nenhuma mensagem de contato ainda.</p>
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Assunto</th>
                                <th>Mensagem</th>
                                <th>Recebido</th>
                                <th>E-mail enviado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contacts as $contact)
                                <tr>
                                    <td>{{ $contact->name }}</td>
                                    <td>{{ $contact->email }}</td>
                                    <td>{{ $contact->subject }}</td>
                                    <td style="max-width: 320px;">{{ \Illuminate\Support\Str::limit($contact->message, 90) }}</td>
                                    <td>{{ $contact->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <form action="{{ route('admin.contact.toggle', $contact) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="{{ $contact->notified ? 'btn-primary' : 'btn-danger' }}"
                                                    style="padding:6px 12px; font-size:.8rem;">
                                                {{ $contact->notified ? 'Sim' : 'Não' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
