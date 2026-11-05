<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TeamMemberSeeder::class,
        ]);

        // Contas de demonstração. Em produção, troque as senhas no primeiro acesso.
        $admin = User::updateOrCreate(
            ['email' => 'admin@hawb.local'],
            [
                'name' => 'Administrador HAWB',
                'password' => 'Admin@HAWB2026',
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_APPROVED,
            ],
        );

        $doctor = User::updateOrCreate(
            ['email' => 'medico@hawb.local'],
            [
                'name' => 'Dra. Helena Ribeiro',
                'password' => 'Medico@HAWB2026',
                'role' => User::ROLE_DOCTOR,
                'specialty' => 'Clínica Geral',
                'crm' => 'CRM-BA 54321',
                'status' => User::STATUS_APPROVED,
                'bio' => 'Médica clínica geral com atuação em atenção primária na região metropolitana de Salvador.',
            ],
        );

        User::updateOrCreate(
            ['email' => 'paciente@hawb.local'],
            [
                'name' => 'Maria da Silva',
                'password' => 'Paciente@HAWB2026',
                'role' => User::ROLE_PATIENT,
                'status' => User::STATUS_APPROVED,
            ],
        );

        // Médico aguardando aprovação no painel admin
        User::updateOrCreate(
            ['email' => 'pendente@hawb.local'],
            [
                'name' => 'Dr. Rafael Nunes',
                'password' => 'Medico@HAWB2026',
                'role' => User::ROLE_DOCTOR,
                'specialty' => 'Cardiologia',
                'crm' => 'CRM-BA 98765',
                'status' => User::STATUS_PENDING,
            ],
        );

        $this->seedPublications($doctor);
        $this->seedConversation($doctor);
    }

    private function seedPublications(User $doctor): void
    {
        if (Publication::where('user_id', $doctor->id)->exists()) {
            return;
        }

        $items = [
            [
                'title' => 'Sinais de alerta que exigem atenção imediata',
                'category' => 'Urgência',
                'content' => "Dor no peito intensa, dificuldade para respirar, fala arrastada ou fraqueza de um lado do corpo são sinais que pedem ajuda imediata.\n\nNesses casos, ligue para o SAMU 192 ou procure a UPA mais próxima. Não espasse a ida ao atendimento esperando os sintomas passarem sozinhos.",
            ],
            [
                'title' => 'Como se preparar para uma consulta na UBS',
                'category' => 'Prevenção',
                'content' => "Leve cartão do SUS, documento com foto e uma lista dos medicamentos que você usa atualmente.\n\nAnote desde quando os sintomas começaram e o que melhora ou piora. Essas informações ajudam o médico a decidir os exames necessários em menos tempo.",
            ],
            [
                'title' => 'Febre alta: quando esperar e quando procurar atendimento',
                'category' => 'Clínica Geral',
                'content' => "Febre acima de 39 °C que não cede com medicação, associada a manchas na pele, confusão mental ou vômitos persistentes, indica a necessidade de avaliação presencial.\n\nEm crianças pequenas e idosos, procure a UPA mesmo com febre moderada, pois a evolução pode ser rápida.",
            ],
        ];

        foreach ($items as $item) {
            $doctor->publications()->create($item + ['is_published' => true]);
        }
    }

    private function seedConversation(User $doctor): void
    {
        $patient = User::where('email', 'paciente@hawb.local')->first();

        if (! $patient) {
            return;
        }

        $conversation = Conversation::firstOrCreate(
            ['user_id' => $patient->id, 'doctor_id' => $doctor->id],
            ['last_message_at' => now()],
        );

        if ($conversation->messages()->exists()) {
            return;
        }

        $turns = [
            [$patient->id, 'Olá, doutora. Estou com febre há três dias e dor de cabeça forte. Devo procurar a UBS?'],
            [$doctor->id, 'Olá! Com febre persistente há três dias, o ideal é avaliação presencial na UBS para investigar a causa. Leve seu cartão do SUS.'],
            [$patient->id, 'Entendi. Devo levar algum exame?'],
            [$doctor->id, 'Se já tiver feito algum exame recente, leve o resultado. Caso contrário, a própria unidade solicita o que for necessário.'],
        ];

        foreach ($turns as $i => [$sender, $body]) {
            $conversation->messages()->create([
                'sender_id' => $sender,
                'body' => $body,
                'created_at' => now()->subMinutes(count($turns) - $i)->subMinutes(5),
            ]);
        }

        $conversation->update(['last_message_at' => now()]);
    }
}
