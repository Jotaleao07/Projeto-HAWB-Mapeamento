# HAWB — Saúde Digital

Projeto do TCC migrado de site estático (HTML/CSS/JS) para uma aplicação **Laravel em arquitetura MVC**, com banco de dados, chat entre pacientes e médicos, formulário de contato e painel administrativo.

## Funcionalidades

- **Páginas institucionais** — Início, Sintomas, Locais (unidades de saúde), Tipos e Equipe, renderizadas em Blade.
- **Equipe no banco de dados** — fotos, tecnologias, bio, LinkedIn e GitHub gerenciados pela tabela `team_members`.
- **Chat / bate-papo**
  - *Publicações médicas* — médicos aprovados publicam conteúdo que todos veem no feed.
  - *Conversas 1-a-1* — pacientes conversam com médicos, com atualização automática por polling.
- **Formulário de contato** — grava no banco e envia e-mail via PHPMailer (SMTP configurado no `.env`; sem SMTP, fica apenas registrado).
- **Autenticação e perfis** — `patient`, `doctor` (aprovação manual pelo admin) e `admin`.
- **Painel administrativo** (`/admin`) — aprova/reprova médicos e acompanha os contatos recebidos.

## Requisitos

- PHP 8.4+ e Composer (ou [Laravel Herd](https://herd.laravel.com), usado neste projeto)
- SQLite (já usado por padrão — sem configuração extra)

## Instalação

```bash
composer install
cp .env.example .env      # se ainda não existir
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve         # ou usar o Herd
```

Com o Herd, o site roda em **https://hawb.test** (o arquivo `hosts` precisa da linha `127.0.0.1 hawb.test`).

## Contas de demonstração

| Perfil   | E-mail              | Senha             |
|----------|---------------------|-------------------|
| Admin    | admin@hawb.local    | Admin@HAWB2026    |
| Médico   | medico@hawb.local   | Medico@HAWB2026   |
| Paciente | paciente@hawb.local | Paciente@HAWB2026 |
| Pendente | pendente@hawb.local | (aguardando aprovação) |

## Estrutura (MVC)

```
app/Http/Controllers/   PageController, AuthController, ChatController,
                        ContactController, AdminController
app/Http/Middleware/    EnsureUserIsAdmin, EnsureUserIsApprovedDoctor
app/Models/             User, Publication, Conversation, Message,
                        Contact, TeamMember
app/Services/           Mailer (wrapper PHPMailer)
database/migrations/    esquema completo (SQLite)
database/seeders/       contas, equipe, publicações e conversas de exemplo
resources/views/        Blade (layouts, pages, chat, auth, admin, contact)
routes/web.php          todas as rotas
public/                 assets (css, js, img)
```

## Contato por e-mail

Edite o `.env` com suas credenciais SMTP para que o PHPMailer envie de fato:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.seuprovedor.com
MAIL_PORT=587
MAIL_USERNAME=seu@email.com
MAIL_PASSWORD=suasenha
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=seu@email.com
MAIL_FROM_NAME="HAWB"
```

Enquanto não houver SMTP, as mensagens ficam registradas no banco e o aviso *"Envio por e-mail ainda não configurado"* é exibido.

## Testado

- 10 rotas públicas → 200
- Login (paciente, médico, admin) → 302
- Envio de mensagem no chat → 302 + JSON atualizado
- Criação de publicação médica → 302 + no feed
- Aprovação de médico no painel admin
- Formulário de contato → gravado no banco + mensagem de sucesso
