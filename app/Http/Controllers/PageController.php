<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Support\Facades\Cache;
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
        $data = $this->unitsData();

        return view('pages.locais', [
            'unitsByCity' => $data['unitsByCity'],
            'cityCoords' => $data['cityCoords'],
            'cities' => $this->bahiaCities(),
        ]);
    }

    /**
     * Todos os 417 municípios da Bahia (lista oficial do IBGE) para o autocomplete.
     * Lido de arquivo local — sem chamada de rede, funciona em qualquer hospedagem.
     * Fallback: as cidades com dados de unidades no projeto.
     *
     * @return list<string>
     */
    private function bahiaCities(): array
    {
        return Cache::rememberForever('ba_cities', function (): array {
            $file = database_path('data/bahia_municipios.json');

            try {
                $raw = is_file($file) ? file_get_contents($file) : false;
                $names = $raw !== false ? json_decode($raw, true) : null;

                if (is_array($names) && $names !== []) {
                    return array_values(array_filter($names, 'is_string'));
                }
            } catch (\Throwable $e) {
                // Arquivo ilegível: segue com o fallback abaixo.
            }

            return ['Camaçari', "Dias d'Ávila", 'Lauro de Freitas', 'Salvador'];
        });
    }

    /**
     * UPAs e UBS de toda a Bahia (fonte CNES — database/data/bahia_unidades.json).
     *
     * Agrupa por cidade para o fluxo de busca (cidade -> UPA/UBS -> detalhe) e
     * devolve também as coordenadas de cada cidade (distância + mapa).
     * Cacheia por filemtime: trocou o JSON, o cache expira sozinho.
     *
     * @return array{unitsByCity: array<string, list<array<string, mixed>>>, cityCoords: array<string, array{0: float, 1: float}>}
     */
    private function unitsData(): array
    {
        $file = database_path('data/bahia_unidades.json');

        if (! is_file($file)) {
            return $this->fallbackUnits();
        }

        try {
            $data = Cache::rememberForever('ba_units_' . (int) @filemtime($file), function () use ($file): ?array {
                $raw = file_get_contents($file);
                $json = $raw !== false ? json_decode($raw, true) : null;

                if (! is_array($json) || ! isset($json['cities']) || ! is_array($json['cities']) || $json['cities'] === []) {
                    return null;
                }

                $unitsByCity = [];
                $cityCoords = [];

                foreach ($json['cities'] as $cidade => $entry) {
                    $units = [];

                    foreach ($entry['units'] ?? [] as $unit) {
                        $units[] = [
                            'id' => $unit['id'] ?? '',
                            'type' => $unit['type'] ?? 'ubs',
                            'cityLabel' => $cidade,
                            'name' => $unit['name'] ?? '',
                            'address' => $unit['address'] ?? '',
                            'phone' => $unit['phone'] ?? 'Não informado',
                            'hours' => $unit['hours'] ?? 'Horário não informado',
                            'services' => $unit['services'] ?? [],
                            'lat' => $unit['lat'] ?? null,
                            'lng' => $unit['lng'] ?? null,
                        ];
                    }

                    if ($units !== []) {
                        $unitsByCity[$cidade] = $units;
                    }

                    if (isset($entry['coords'][0], $entry['coords'][1])) {
                        $cityCoords[$cidade] = [$entry['coords'][0], $entry['coords'][1]];
                    }
                }

                return ['unitsByCity' => $unitsByCity, 'cityCoords' => $cityCoords];
            });
        } catch (\Throwable $e) {
            $data = null;
        }

        return is_array($data) ? $data : $this->fallbackUnits();
    }

    /**
     * Fallback: unidades de exemplo caso o arquivo CNES esteja ausente.
     *
     * @return array{unitsByCity: array<string, list<array<string, mixed>>>, cityCoords: array<string, array{0: float, 1: float}>}
     */
    private function fallbackUnits(): array
    {
        $unitsByCity = [];
        foreach ($this->healthUnits() as $unit) {
            $unitsByCity[$unit['cityLabel']][] = $unit;
        }

        return [
            'unitsByCity' => $unitsByCity,
            'cityCoords' => [
                'Salvador' => [-12.9777, -38.5016],
                'Camaçari' => [-12.6975, -38.3161],
                'Lauro de Freitas' => [-12.8935, -38.3238],
                "Dias d'Ávila" => [-12.6167, -38.2833],
            ],
        ];
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
