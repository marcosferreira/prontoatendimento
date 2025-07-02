<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LogradouroSeeder extends Seeder
{
    public function run()
    {
        // Buscar alguns bairros para associar logradouros
        $bairroModel = new \App\Models\BairroModel();
        $bairros = $bairroModel->findAll();
        
        if (empty($bairros)) {
            echo "Nenhum bairro encontrado. Execute o BairroSeeder primeiro.\n";
            return;
        }

        // Criar logradouros para alguns bairros urbanos
        $bairroCentro = null;
        $bairroGloria = null;
        $bairroNovaConquista = null;
        
        foreach ($bairros as $bairro) {
            if ($bairro['nome_bairro'] === 'Bairro Centro') {
                $bairroCentro = $bairro;
            } elseif ($bairro['nome_bairro'] === 'Bairro Glória') {
                $bairroGloria = $bairro;
            } elseif ($bairro['nome_bairro'] === 'Bairro Nova Conquista') {
                $bairroNovaConquista = $bairro;
            }
        }

        $data = [];
        
        // Logradouros do Centro
        if ($bairroCentro) {
            $data = array_merge($data, [
                [
                    'nome_logradouro' => 'da Independência',
                    'tipo_logradouro' => 'Rua',
                    'cep' => '58775-000',
                    'id_bairro' => $bairroCentro['id_bairro'],
                    'observacoes' => 'Rua principal do centro',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'nome_logradouro' => 'Presidente Vargas',
                    'tipo_logradouro' => 'Avenida',
                    'cep' => '58775-001',
                    'id_bairro' => $bairroCentro['id_bairro'],
                    'observacoes' => 'Avenida central',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'nome_logradouro' => 'da Matriz',
                    'tipo_logradouro' => 'Praça',
                    'cep' => '58775-002',
                    'id_bairro' => $bairroCentro['id_bairro'],
                    'observacoes' => 'Praça central da cidade',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'nome_logradouro' => 'João Pessoa',
                    'tipo_logradouro' => 'Rua',
                    'cep' => '58775-003',
                    'id_bairro' => $bairroCentro['id_bairro'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'nome_logradouro' => 'XV de Novembro',
                    'tipo_logradouro' => 'Rua',
                    'cep' => '58775-004',
                    'id_bairro' => $bairroCentro['id_bairro'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ]);
        }

        // Logradouros do Bairro Glória
        if ($bairroGloria) {
            $data = array_merge($data, [
                [
                    'nome_logradouro' => 'da Glória',
                    'tipo_logradouro' => 'Rua',
                    'cep' => '58775-010',
                    'id_bairro' => $bairroGloria['id_bairro'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'nome_logradouro' => 'São Francisco',
                    'tipo_logradouro' => 'Rua',
                    'cep' => '58775-011',
                    'id_bairro' => $bairroGloria['id_bairro'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'nome_logradouro' => 'das Flores',
                    'tipo_logradouro' => 'Travessa',
                    'cep' => '58775-012',
                    'id_bairro' => $bairroGloria['id_bairro'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ]);
        }

        // Logradouros do Nova Conquista
        if ($bairroNovaConquista) {
            $data = array_merge($data, [
                [
                    'nome_logradouro' => 'da Conquista',
                    'tipo_logradouro' => 'Rua',
                    'cep' => '58775-020',
                    'id_bairro' => $bairroNovaConquista['id_bairro'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'nome_logradouro' => 'Nova Esperança',
                    'tipo_logradouro' => 'Rua',
                    'cep' => '58775-021',
                    'id_bairro' => $bairroNovaConquista['id_bairro'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'nome_logradouro' => 'do Progresso',
                    'tipo_logradouro' => 'Avenida',
                    'cep' => '58775-022',
                    'id_bairro' => $bairroNovaConquista['id_bairro'],
                    'observacoes' => 'Avenida principal do bairro',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ]);
        }

        // Logradouros rurais (alguns sítios)
        $sitioBrejinho = null;
        $sitioItabaiana = null;
        
        foreach ($bairros as $bairro) {
            if ($bairro['nome_bairro'] === 'Sítio Brejinho') {
                $sitioBrejinho = $bairro;
            } elseif ($bairro['nome_bairro'] === 'Sítio Itabaiana') {
                $sitioItabaiana = $bairro;
            }
        }

        if ($sitioBrejinho) {
            $data = array_merge($data, [
                [
                    'nome_logradouro' => 'Principal do Brejinho',
                    'tipo_logradouro' => 'Estrada',
                    'id_bairro' => $sitioBrejinho['id_bairro'],
                    'observacoes' => 'Estrada de acesso ao sítio',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ]);
        }

        if ($sitioItabaiana) {
            $data = array_merge($data, [
                [
                    'nome_logradouro' => 'da Itabaiana',
                    'tipo_logradouro' => 'Estrada',
                    'id_bairro' => $sitioItabaiana['id_bairro'],
                    'observacoes' => 'Estrada rural de acesso',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ]);
        }

        if (!empty($data)) {
            // Inserir os dados
            $this->db->table('logradouros')->insertBatch($data);
            echo "Logradouros inseridos com sucesso!\n";
        } else {
            echo "Nenhum logradouro foi inserido. Verifique se os bairros existem.\n";
        }
    }
}
