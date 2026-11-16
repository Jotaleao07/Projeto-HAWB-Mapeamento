<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class PageController extends Controller
{
    public function inicio(): View
    {
        return view('pages.inicio');
    }

    public function sintomas(): View
    {
        return view('pages.sintomas');
    }

    public function locais(): View
    {
        $locations = $this->healthUnits();

        // Agrupa as unidades por cidade para o fluxo de busca (cidade -> UPA/UBS -> detalhe).
        $unitsByCity = [];
        foreach ($locations as $unit) {
            $unitsByCity[$unit['cityLabel']][] = $unit;
        }

        return view('pages.locais', [
            'locations' => $locations,
            'unitsByCity' => $unitsByCity,
            'cities' => $this->bahiaCities(),
        ]);
    }

    /**
     * Todos os municípios da Bahia (IBGE) para o autocomplete da busca de cidade.
     * Cache de 1 dia; se a API falhar, cai para as cidades com dados no projeto.
     *
     * @return list<string>
     */
    private function bahiaCities(): array
    {
        return Cache::remember('ba_cities', now()->addDay(), function (): array {
            try {
                $response = Http::timeout(8)
                    ->withHeaders(['Accept' => 'application/json'])
                    ->get('https://servicodados.ibge.gov.br/api/v1/localidades/estados/29/municipios');

                if ($response->ok()) {
                    $names = collect($response->json())
                        ->pluck('nome')
                        ->filter()
                        ->unique()
                        ->sort()
                        ->values()
                        ->all();

                    if ($names !== []) {
                        return $names;
                    }
                }
            } catch (\Throwable $e) {
                // API indisponível: segue com o fallback abaixo.
            }

            return ['Camaçari', "Dias d'Ávila", 'Lauro de Freitas', 'Salvador'];
        });
    }

    /**
     * Unidades de saúde da rede pública da região metropolitana de Salvador.
     *
     * @return list<array<string, mixed>>
     */
    private function healthUnits(): array
    {
        return [
            ['id' => 'brotas', 'type' => 'upa', 'city' => 'salvador', 'cityLabel' => 'Salvador', 'name' => 'UPA Brotas',
                'address' => 'Rua Catarina Paraguacu - Brotas', 'phone' => '(71) 3611-1500', 'hours' => '24 horas',
                'services' => ['Urgência', 'Emergência', 'Raio-X', 'Laboratório']],

            ['id' => 'cristovao', 'type' => 'upa', 'city' => 'salvador', 'cityLabel' => 'Salvador', 'name' => 'UPA São Cristóvão',
                'address' => 'Rua Direta de São Cristóvão - São Cristóvão', 'phone' => '(71) 3611-1600', 'hours' => '24 horas',
                'services' => ['Urgência', 'Emergência', 'Raio-X', 'Eletrocardiograma']],

            ['id' => 'carlos-gomes', 'type' => 'ubs', 'city' => 'salvador', 'cityLabel' => 'Salvador', 'name' => 'UBS Carlos Gomes',
                'address' => 'Rua Carlos Gomes, 270 - Centro', 'phone' => '(71) 3202-1234', 'hours' => '07:00 às 17:00',
                'services' => ['Vacinação', 'Consultas', 'Pré-Natal', 'Preventivo']],

            ['id' => 'itinga', 'type' => 'upa', 'city' => 'lauro', 'cityLabel' => 'Lauro de Freitas', 'name' => 'UPA Itinga',
                'address' => 'Avenida São Cristóvão - Itinga', 'phone' => '(71) 3378-4500', 'hours' => '24 horas',
                'services' => ['Urgência', 'Ortopedia', 'Pediatria', 'Raio-X']],

            ['id' => 'praiana', 'type' => 'ubs', 'city' => 'lauro', 'cityLabel' => 'Lauro de Freitas', 'name' => 'UBS Vila Praiana',
                'address' => 'Rua Praia de Pajussara - Vila Praiana', 'phone' => '(71) 3288-8900', 'hours' => '07:00 às 16:00',
                'services' => ['Clínica Geral', 'Enfermagem', 'Dentista']],

            ['id' => 'gleba-a', 'type' => 'upa', 'city' => 'camacari', 'cityLabel' => 'Camaçari', 'name' => 'UPA Gleba A',
                'address' => 'Avenida Concêntrica - Gleba A', 'phone' => '(71) 3622-7700', 'hours' => '24 horas',
                'services' => ['Urgência', 'Suturas', 'Medicação', 'Pediatria']],

            ['id' => 'phoc', 'type' => 'ubs', 'city' => 'camacari', 'cityLabel' => 'Camaçari', 'name' => 'UBS Phoc II',
                'address' => 'Rua Pará, Phoc II - Camaçari', 'phone' => '(71) 3622-5511', 'hours' => '07:00 às 17:00',
                'services' => ['Hiperdia', 'Vacinação', 'Curativos']],

            ['id' => 'upa-dias', 'type' => 'upa', 'city' => 'dias', 'cityLabel' => "Dias d'Ávila", 'name' => "UPA Dias d'Ávila",
                'address' => 'Avenida Raul Seixas, Centro', 'phone' => '(71) 3648-3500', 'hours' => '24 horas',
                'services' => ['Urgência', 'Estabilização', 'Exames de Sangue']],

            ['id' => 'concordia', 'type' => 'ubs', 'city' => 'dias', 'cityLabel' => "Dias d'Ávila", 'name' => 'UBS Concórdia',
                'address' => 'Rua da Concórdia - Bairro Concórdia', 'phone' => '(71) 3648-1199', 'hours' => '08:00 às 16:00',
                'services' => ['Distribuição de Medicamentos', 'Acompanhamento']],
        ];
    }

    public function tipos(): View
    {
        return view('pages.tipos');
    }

    public function equipe(): View
    {
        $members = TeamMember::orderBy('order')->get();

        // Payload já serializado para o JS da página — evita expressões complexas no Blade.
        $membersJs = $members->mapWithKeys(function (TeamMember $member) {
            return [
                (string) $member->id => [
                    'nome' => $member->name,
                    'cargo' => $member->role,
                    'bio' => $member->bio ?? '',
                    'skills' => $member->skills ?? [],
                    'linkedin' => $member->linkedin,
                    'github' => $member->github,
                ],
            ];
        })->all();

        return view('pages.equipe', compact('members', 'membersJs'));
    }
}
