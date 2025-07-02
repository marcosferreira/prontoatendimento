<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BairroSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Bairros Urbanos
            [
                'nome_bairro' => 'Bairro Centro',
                'area' => 'Urbana',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Bairro Glória',
                'area' => 'Urbana',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Bairro Governador José Maranhão',
                'area' => 'Urbana',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Bairro Jardim Primavera',
                'area' => 'Urbana',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Bairro Nova Cidade',
                'area' => 'Urbana',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Bairro Nova Conquista',
                'area' => 'Urbana',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Bairro São Pedro',
                'area' => 'Urbana',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Bairro Tapuio',
                'area' => 'Urbana',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Bairro Terra Prometida',
                'area' => 'Urbana',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Comunidades e Sítios Rurais
            [
                'nome_bairro' => 'Comunidade Cruz da Menina',
                'area' => 'Rural - Área 05',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Balanço',
                'area' => 'Rural - Área 01',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Barbatão',
                'area' => 'Rural - Área 03',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Barroção',
                'area' => 'Rural - Área 05',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Boa Vista',
                'area' => 'Rural - Área 06',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Bogi',
                'area' => 'Rural - Área 02',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Brejinho',
                'area' => 'Rural - Área 04',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Caboclo de Palhares',
                'area' => 'Rural - Área 03',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Caco',
                'area' => 'Rural - Área 04',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Cafundó',
                'area' => 'Rural - Área 05',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Caiana',
                'area' => 'Rural - Área 05',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Caiçara',
                'area' => 'Rural - Área 02',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Cajazeiras',
                'area' => 'Rural - Área 05',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Canafistula',
                'area' => 'Rural - Área 04',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Capivara',
                'area' => 'Rural - Área 01',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Carnaúba',
                'area' => 'Rural - Área 06',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Carnaúbeira de Cima',
                'area' => 'Rural - Área 06',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Chã de Palhares',
                'area' => 'Rural - Área 03',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Cobra Maga',
                'area' => 'Rural - Área 01',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Cozinha',
                'area' => 'Rural - Área 02',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Cruz',
                'area' => 'Rural - Área 04',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Estreito',
                'area' => 'Rural - Área 06',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Estrela',
                'area' => 'Rural - Área 01',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Glória',
                'area' => 'Rural - Área 03',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Itabaiana',
                'area' => 'Rural - Área 01',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Lagoa da Serra',
                'area' => 'Rural - Área 06',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Lagoa do Braz',
                'area' => 'Rural - Área 04',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Lajedo Preto',
                'area' => 'Rural - Área 05',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Marcação',
                'area' => 'Rural - Área 05',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Marias Pretas',
                'area' => 'Rural - Área 02',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Massaranduba',
                'area' => 'Rural - Área 03',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Mata',
                'area' => 'Rural - Área 03',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Mela Bode',
                'area' => 'Rural - Área 06',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Miguel',
                'area' => 'Rural - Área 01',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Mulungu',
                'area' => 'Rural - Área 05',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Oiticica',
                'area' => 'Rural - Área 06',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Olho D\'Água do Gregório',
                'area' => 'Rural - Área 06',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Panelas',
                'area' => 'Rural - Área 02',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Pedra Lavrada',
                'area' => 'Rural - Área 05',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Pedra Lisa',
                'area' => 'Rural - Área 04',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Pimenta',
                'area' => 'Rural - Área 04',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Pinhões',
                'area' => 'Rural - Área 02',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Pitomba',
                'area' => 'Rural - Área 04',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Queimadas',
                'area' => 'Rural - Área 05',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Raimundo',
                'area' => 'Rural - Área 04',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Raposa',
                'area' => 'Rural - Área 02',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Riacho de Areia',
                'area' => 'Rural - Área 01',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Salgadinho',
                'area' => 'Rural - Área 05',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Salgado de Manoel Moreira',
                'area' => 'Rural - Área 02',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio São Luiz',
                'area' => 'Rural - Área 01',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Seixos',
                'area' => 'Rural - Área 05',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Seró',
                'area' => 'Rural - Área 04',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Serra do Sítio',
                'area' => 'Rural - Área 06',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Simão',
                'area' => 'Rural - Área 01',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Tanque do Veado',
                'area' => 'Rural - Área 03',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Tanques',
                'area' => 'Rural - Área 05',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Tapuio',
                'area' => 'Rural - Área 04',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Umari',
                'area' => 'Rural - Área 03',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Umarizinho',
                'area' => 'Rural - Área 04',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Vaca Morta',
                'area' => 'Rural - Área 03',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Várzea Grande',
                'area' => 'Rural - Área 02',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Volta',
                'area' => 'Rural - Área 06',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Zé de Fogo',
                'area' => 'Rural - Área 03',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Zé Paz da Serra',
                'area' => 'Rural - Área 03',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Zé Paz de Baixo',
                'area' => 'Rural - Área 02',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Sítio Zé Paz de Cima',
                'area' => 'Rural - Área 02',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Inserir os dados
        $this->db->table('bairros')->insertBatch($data);
    }
}
