<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BairroSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nome_bairro' => 'Centro',
                'area' => 'Região Central',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Vila Nova',
                'area' => 'Zona Norte',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Jardim América',
                'area' => 'Zona Sul',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Santa Rita',
                'area' => 'Zona Leste',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'São José',
                'area' => 'Zona Oeste',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Industrial',
                'area' => 'Zona Industrial',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nome_bairro' => 'Cohab',
                'area' => 'Habitacional',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Inserir os dados
        $this->db->table('bairros')->insertBatch($data);
    }
}
