<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        // Tecnologias do projeto: HTML/CSS/JS no front, PHP + Laravel + SQLite/PostgreSQL no back.
        $stack = [
            ['icon' => 'devicon-html5-plain colored', 'label' => 'HTML5'],
            ['icon' => 'devicon-css3-plain colored', 'label' => 'CSS3'],
            ['icon' => 'devicon-javascript-plain colored', 'label' => 'JavaScript'],
            ['icon' => 'devicon-php-plain colored', 'label' => 'PHP'],
            ['icon' => 'devicon-laravel-plain colored', 'label' => 'Laravel'],
            ['icon' => 'devicon-sqlite-original colored', 'label' => 'SQLite'],
            ['icon' => 'devicon-github-original colored', 'label' => 'GitHub'],
            ['icon' => 'devicon-figma-plain colored', 'label' => 'Figma'],
        ];

        $members = [
            [
                'name' => 'João Victor Leão Silva',
                'role' => 'Desenvolvedor full-stack',
                'bio' => 'Técnico em informática para internet. Responsável pela estruturação do front-end e pela integração com o Laravel.',
                'photo' => 'jota.png',
                'linkedin' => 'https://www.linkedin.com/in/JOTALEAO07',
                'github' => 'https://github.com/Jotaleao07',
            ],
            [
                'name' => 'Ariel França',
                'role' => 'Product Owner & Desenvolvedor full-stack',
                'bio' => 'Técnico em informática para internet. Definição de escopo, priorização de requisitos e desenvolvimento full-stack.',
                'photo' => 'Ariel.png',
                'linkedin' => 'https://www.linkedin.com/in/PERFIL-ARIEL',
                'github' => 'https://github.com/USUARIO-ARIEL',
            ],
            [
                'name' => 'Derick Souza',
                'role' => 'Desenvolvedor full-stack',
                'bio' => 'Técnico em informática para internet. Desenvolvimento das telas de análise de sintomas e mapeamento de locais.',
                'photo' => 'Derick.png',
                'linkedin' => 'https://www.linkedin.com/in/PERFIL-DERICK',
                'github' => 'https://github.com/USUARIO-DERICK',
            ],
            [
                'name' => 'Adryano Silva',
                'role' => 'Desenvolvedor full-stack',
                'bio' => 'Técnico em informática para internet. Desenvolvimento das telas de especialidades médicas e calculadora de IMC.',
                'photo' => 'Adryano.jpg',
                'linkedin' => 'https://www.linkedin.com/in/PERFIL-ADRYANO',
                'github' => 'https://github.com/USUARIO-ADRYANO',
            ],
            [
                'name' => 'Matheus Costa',
                'role' => 'Desenvolvedor full-stack',
                'bio' => 'Técnico em informática para internet. Modelagem do banco de dados e implementação das rotas do chat com médicos.',
                'photo' => 'Matheus.png',
                'linkedin' => 'https://www.linkedin.com/in/PERFIL-MATHEUS',
                'github' => 'https://github.com/USUARIO-MATHEUS',
            ],
            [
                'name' => 'Ana Luisa',
                'role' => 'Professora e desenvolvedora | Auxiliar do projeto',
                'bio' => 'Engenheira de software | Professora de computação | Pesquisadora | Microsoft Certified Educator | Redadora de Conteúdo Web | Palestrante.',
                'photo' => 'Professora.png',
                'linkedin' => 'https://www.linkedin.com/in/PERFIL-ANA-LUISA',
                'github' => 'https://github.com/USUARIO-ANA-LUISA',
                'extra' => [
                    ['icon' => 'devicon-postgresql-plain colored', 'label' => 'PostgreSQL'],
                    ['icon' => 'devicon-nodejs-plain colored', 'label' => 'Node.js'],
                ],
            ],
        ];

        foreach ($members as $index => $member) {
            TeamMember::updateOrCreate(
                ['name' => $member['name']],
                [
                    'role' => $member['role'],
                    'bio' => $member['bio'],
                    'photo' => $member['photo'],
                    'skills' => array_merge($stack, $member['extra'] ?? []),
                    'linkedin' => $member['linkedin'],
                    'github' => $member['github'],
                    'order' => $index,
                ],
            );
        }
    }
}
