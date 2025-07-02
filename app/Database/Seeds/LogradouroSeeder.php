<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LogradouroSeeder extends Seeder
{
    public function run()
    {
        // Buscar todos os bairros para associar logradouros
        $bairroModel = new \App\Models\BairroModel();
        $bairros = $bairroModel->findAll();
        
        if (empty($bairros)) {
            echo "Nenhum bairro encontrado. Execute o BairroSeeder primeiro.\n";
            return;
        }

        // Criar mapeamento de bairros por nome
        $bairrosMap = [];
        foreach ($bairros as $bairro) {
            $bairrosMap[$bairro['nome_bairro']] = $bairro;
        }

        $data = [];
        $currentDateTime = date('Y-m-d H:i:s');
        
        // Logradouros do CENTRO
        if (isset($bairrosMap['Bairro Centro'])) {
            $idBairro = $bairrosMap['Bairro Centro']['id_bairro'];
            $logradourosCentro = [
                ['Major Augusto Bezerra', 'Avenida', '58228-000'],
                ['Manoel Pedro', 'Avenida', '58228-000'],
                ['Ana da Conceição Melo', 'Rua', '58228-000'],
                ['Antônio Rafael', 'Rua', '58228-000'],
                ['Antônio Toscano de Araújo', 'Rua', '58228-000'],
                ['Benedito Francisco Alves', 'Rua', '58228-000'],
                ['Gerôncio Ribeiro da Silva', 'Rua', '58228-000'],
                ['João Quirino de Oliveira', 'Rua', '58228-000'],
                ['José Carolino', 'Rua', '58228-000'],
                ['José Paulino', 'Rua', '58228-000'],
                ['Luiz Justino de Araújo', 'Rua', '58228-000'],
                ['Manoel Ferreira de Lima (Manoel Praeiro)', 'Rua', '58228-000'],
                ['Manoel Leonel da Costa', 'Rua', '58228-000'],
                ['Pedro Teixeira', 'Rua', '58228-000'],
                ['Presidente João Pessoa', 'Rua', '58228-000'],
                ['Professor Odilon Matias de Araújo', 'Rua', '58228-000'],
                ['Vereador Manoel Alves de Lima', 'Rua', '58228-000'],
                ['Lourival José do Nascimento', 'Rua', '58228-000']
            ];

            foreach ($logradourosCentro as $logradouro) {
                $data[] = [
                    'nome_logradouro' => $logradouro[0],
                    'tipo_logradouro' => $logradouro[1],
                    'cep' => $logradouro[2],
                    'id_bairro' => $idBairro,
                    'observacoes' => null,
                    'created_at' => $currentDateTime,
                    'updated_at' => $currentDateTime
                ];
            }
        }

        // Logradouros do GLÓRIA
        if (isset($bairrosMap['Bairro Glória'])) {
            $idBairro = $bairrosMap['Bairro Glória']['id_bairro'];
            $logradourosGloria = [
                ['Hermínio Justino de Araújo', 'Rua', '58228-000'],
                ['José Hermínio de Araújo', 'Rua', '58228-000'],
                ['Luiz José do Nascimento', 'Rua', '58228-000'],
                ['Lourival José do Nascimento', 'Rua', '58228-000'],
                ['Manoel Leonel da Costa', 'Rua', '58228-000']
            ];

            foreach ($logradourosGloria as $logradouro) {
                $data[] = [
                    'nome_logradouro' => $logradouro[0],
                    'tipo_logradouro' => $logradouro[1],
                    'cep' => $logradouro[2],
                    'id_bairro' => $idBairro,
                    'observacoes' => null,
                    'created_at' => $currentDateTime,
                    'updated_at' => $currentDateTime
                ];
            }
        }

        // Logradouros do GOVERNADOR JOSÉ MARANHÃO
        if (isset($bairrosMap['Bairro Governador José Maranhão'])) {
            $idBairro = $bairrosMap['Bairro Governador José Maranhão']['id_bairro'];
            $logradourosMaranhao = [
                ['João Malaquias de Araújo', 'Rua', '58228-000'],
                ['José de Azevedo Maia', 'Rua', '58228-000'],
                ['Josué Lucas Neto', 'Rua', '58228-000'],
                ['Juiz de Direito Manoel Alves Irmão (Neneu Ramo)', 'Rua', '58228-000'],
                ['Lindalva Ferreira da Silva', 'Rua', '58228-000'],
                ['Maria Alves de Araújo', 'Rua', '58228-000'],
                ['Maria Rita Conceição', 'Rua', '58228-000'],
                ['Prefeito José Eugênio Cabral de Melo', 'Rua', '58228-000']
            ];

            foreach ($logradourosMaranhao as $logradouro) {
                $data[] = [
                    'nome_logradouro' => $logradouro[0],
                    'tipo_logradouro' => $logradouro[1],
                    'cep' => $logradouro[2],
                    'id_bairro' => $idBairro,
                    'observacoes' => null,
                    'created_at' => $currentDateTime,
                    'updated_at' => $currentDateTime
                ];
            }
        }

        // Logradouros do JARDIM PRIMAVERA
        if (isset($bairrosMap['Bairro Jardim Primavera'])) {
            $idBairro = $bairrosMap['Bairro Jardim Primavera']['id_bairro'];
            $logradourosPrimavera = [
                ['Major Augusto Bezerra', 'Avenida', '58228-000'],
                ['Anézio Ferreira de Lima', 'Rua', '58228-000'],
                ['Antônio Braz dos Santos', 'Rua', '58228-000'],
                ['Cecílio Francisco da Silva', 'Rua', '58228-000'],
                ['Cicero Camelo de Melo', 'Rua', '58228-000'],
                ['Francisco Luiz Soares', 'Rua', '58228-000'],
                ['José Antônio Teixeira', 'Rua', '58228-000'],
                ['José Henrique de Oliveira', 'Rua', '58228-000'],
                ['Luiz Pedro da Costa', 'Rua', '58228-000'],
                ['Maria Helena de Jesus', 'Rua', '58228-000'],
                ['Severino Gomes de Araújo', 'Rua', '58228-000'],
                ['Tabelião José Cantalice Moreira', 'Rua', '58228-000'],
                ['Vereador Pedro José da Costa', 'Rua', '58228-000']
            ];

            foreach ($logradourosPrimavera as $logradouro) {
                $data[] = [
                    'nome_logradouro' => $logradouro[0],
                    'tipo_logradouro' => $logradouro[1],
                    'cep' => $logradouro[2],
                    'id_bairro' => $idBairro,
                    'observacoes' => null,
                    'created_at' => $currentDateTime,
                    'updated_at' => $currentDateTime
                ];
            }
        }

        // Logradouros do NOVA CIDADE
        if (isset($bairrosMap['Bairro Nova Cidade'])) {
            $idBairro = $bairrosMap['Bairro Nova Cidade']['id_bairro'];
            $logradourosNovaCidade = [
                ['Anézio Ferreira de Lima', 'Rua', '58228-000'],
                ['Antônio Daniel da Silva', 'Rua', '58228-000'],
                ['Ex. Combatente Severino Alexandre de Lima', 'Rua', '58228-000'],
                ['Francisco Albino da Silva', 'Rua', '58228-000'],
                ['José Antônio Teixeira', 'Rua', '58228-000'],
                ['Manoel Ângelo', 'Rua', '58228-000'],
                ['Manoel José da Silva', 'Rua', '58228-000'],
                ['Manoel Pereira de Aquino', 'Rua', '58228-000'],
                ['Maria Ridete Pereira de Aquino', 'Rua', '58228-000'],
                ['Prefeito Antônio Luiz de Araújo', 'Rua', '58228-000'],
                ['Sebastião Paulino da Costa', 'Rua', '58228-000'],
                ['Tabelião José Cantalice Moreira', 'Rua', '58228-000']
            ];

            foreach ($logradourosNovaCidade as $logradouro) {
                $data[] = [
                    'nome_logradouro' => $logradouro[0],
                    'tipo_logradouro' => $logradouro[1],
                    'cep' => $logradouro[2],
                    'id_bairro' => $idBairro,
                    'observacoes' => null,
                    'created_at' => $currentDateTime,
                    'updated_at' => $currentDateTime
                ];
            }
        }

        // Logradouros do NOVA CONQUISTA
        if (isset($bairrosMap['Bairro Nova Conquista'])) {
            $idBairro = $bairrosMap['Bairro Nova Conquista']['id_bairro'];
            $logradourosNovaConquista = [
                ['Nivaldo Cândido de Araújo', 'Praça', '58228-000'],
                ['Antônio Pedro da Silva', 'Rua', '58228-000'],
                ['Antônio Pereira da Costa', 'Rua', '58228-000'],
                ['Antônio Rafael', 'Rua', '58228-000'],
                ['Antônio Toscano de Araújo', 'Rua', '58228-000'],
                ['Arão Lucas de Araújo', 'Rua', '58228-000'],
                ['Benedito Pedro Pinheiro Borges', 'Rua', '58228-000'],
                ['Francisco Enedino da Silva', 'Rua', '58228-000'],
                ['Francisco Ferreira de Lima Neto', 'Rua', '58228-000'],
                ['José Esperidião da Silva', 'Rua', '58228-000'],
                ['José Hermínio de Araújo', 'Rua', '58228-000'],
                ['José Roberto Idalino', 'Rua', '58228-000'],
                ['José Rodrigues de Lima', 'Rua', '58228-000'],
                ['Josué Lucas de Araújo', 'Rua', '58228-000'],
                ['Júlia Gomes de Araújo', 'Rua', '58228-000'],
                ['Luiz Ferreira de Lima', 'Rua', '58228-000'],
                ['Manoel Borges de Morais', 'Rua', '58228-000'],
                ['Pedro Ferreira de Araújo', 'Rua', '58228-000'],
                ['Pedro João do Nascimento', 'Rua', '58228-000'],
                ['Prefeito Francisco Avelino da Silva', 'Rua', '58228-000'],
                ['Tabelião Maviael Alves Moreira', 'Rua', '58228-000']
            ];

            foreach ($logradourosNovaConquista as $logradouro) {
                $data[] = [
                    'nome_logradouro' => $logradouro[0],
                    'tipo_logradouro' => $logradouro[1],
                    'cep' => $logradouro[2],
                    'id_bairro' => $idBairro,
                    'observacoes' => null,
                    'created_at' => $currentDateTime,
                    'updated_at' => $currentDateTime
                ];
            }
        }

        // Logradouros do SÃO PEDRO
        if (isset($bairrosMap['Bairro São Pedro'])) {
            $idBairro = $bairrosMap['Bairro São Pedro']['id_bairro'];
            $logradourosSaoPedro = [
                ['Agenor de Azevedo Maia', 'Rua', '58228-000'],
                ['Antônio Toscano de Araújo', 'Rua', '58228-000'],
                ['Assis Claudino do Nascimento', 'Rua', '58228-000'],
                ['Gabriel Bento de Lima', 'Rua', '58228-000'],
                ['Gerôncio Ribeiro da Silva', 'Rua', '58228-000'],
                ['Joaquim Pinheiro Borges', 'Rua', '58228-000'],
                ['José Antônio da Silva', 'Rua', '58228-000'],
                ['Maria da Glória Cantalice Moreira', 'Rua', '58228-000'],
                ['Maria Ribeiro da Silva', 'Rua', '58228-000'],
                ['Prefeito Joaquim Cabral de Melo', 'Rua', '58228-000'],
                ['Prefeito José Tomaz de Aquino', 'Rua', '58228-000'],
                ['Vereador Manoel Alves de Lima', 'Rua', '58228-000']
            ];

            foreach ($logradourosSaoPedro as $logradouro) {
                $data[] = [
                    'nome_logradouro' => $logradouro[0],
                    'tipo_logradouro' => $logradouro[1],
                    'cep' => $logradouro[2],
                    'id_bairro' => $idBairro,
                    'observacoes' => null,
                    'created_at' => $currentDateTime,
                    'updated_at' => $currentDateTime
                ];
            }
        }

        // Logradouros do TAPUIO
        if (isset($bairrosMap['Bairro Tapuio'])) {
            $idBairro = $bairrosMap['Bairro Tapuio']['id_bairro'];
            $logradourosTapuio = [
                ['Ana de Souza Maciel', 'Rua', '58228-000'],
                ['Francisco Adolfo de Souza', 'Rua', '58228-000'],
                ['Manoel Luiz Soares', 'Rua', '58228-000'],
                ['Padre Luiz Deodato Jundbauer', 'Rua', '58228-000']
            ];

            foreach ($logradourosTapuio as $logradouro) {
                $data[] = [
                    'nome_logradouro' => $logradouro[0],
                    'tipo_logradouro' => $logradouro[1],
                    'cep' => $logradouro[2],
                    'id_bairro' => $idBairro,
                    'observacoes' => null,
                    'created_at' => $currentDateTime,
                    'updated_at' => $currentDateTime
                ];
            }
        }

        // Logradouros do TERRA PROMETIDA
        if (isset($bairrosMap['Bairro Terra Prometida'])) {
            $idBairro = $bairrosMap['Bairro Terra Prometida']['id_bairro'];
            $logradourosTerraPrometida = [
                ['Major Augusto Bezerra', 'Avenida', '58228-000'],
                ['Alfredo Cantalice', 'Rua', '58228-000'],
                ['Ana de Souza Maciel', 'Rua', '58228-000'],
                ['Antonio Joaquim Silvestre', 'Rua', '58228-000'],
                ['Cícero Noé', 'Rua', '58228-000'],
                ['Elba Maria da Silva', 'Rua', '58228-000'],
                ['Ernesto Ramos', 'Rua', '58228-000'],
                ['José Antônio da Silva', 'Rua', '58228-000'],
                ['Pedro Paulino Ferreira da Costa', 'Rua', '58228-000'],
                ['Praça do Trabalhador', 'Rua', '58228-000'],
                ['Vereador José Fabiano da Costa Teixeira', 'Rua', '58228-000']
            ];

            foreach ($logradourosTerraPrometida as $logradouro) {
                $data[] = [
                    'nome_logradouro' => $logradouro[0],
                    'tipo_logradouro' => $logradouro[1],
                    'cep' => $logradouro[2],
                    'id_bairro' => $idBairro,
                    'observacoes' => null,
                    'created_at' => $currentDateTime,
                    'updated_at' => $currentDateTime
                ];
            }
        }

        // Logradouros rurais para todos os sítios
        $sitiosComLogradouros = [
            // Comunidades
            'Comunidade Cruz da Menina' => [
                ['Cruz da Menina', 'Sítio', '58228-000', 'Estrada de acesso à comunidade']
            ],
            
            // Sítios - Área 01
            'Sítio Balanço' => [
                ['do Balanço', 'Sítio', '58228-000', 'Estrada rural de acesso']
            ],
            'Sítio Capivara' => [
                ['do Capivara', 'Sítio', '58228-000', 'Acesso rural']
            ],
            'Sítio Cobra Maga' => [
                ['Cobra Maga', 'Sítio', '58228-000', 'Acesso rural']
            ],
            'Sítio Estrela' => [
                ['da Estrela', 'Sítio', '58228-000', 'Estrada rural']
            ],
            'Sítio Itabaiana' => [
                ['da Itabaiana', 'Sítio', '58228-000', 'Estrada rural de acesso']
            ],
            'Sítio Miguel' => [
                ['do Miguel', 'Sítio', '58228-000', 'Estrada de acesso']
            ],
            'Sítio Riacho de Areia' => [
                ['Riacho de Areia', 'Sítio', '58228-000', 'Estrada do sítio']
            ],
            'Sítio São Luiz' => [
                ['São Luiz', 'Sítio', '58228-000', 'Acesso ao sítio']
            ],
            'Sítio Simão' => [
                ['do Simão', 'Sítio', '58228-000', 'Estrada rural']
            ],
            
            // Sítios - Área 02
            'Sítio Bogi' => [
                ['do Bogi', 'Sítio', '58228-000', 'Estrada de acesso rural']
            ],
            'Sítio Caiçara' => [
                ['da Caiçara', 'Sítio', '58228-000', 'Acesso rural']
            ],
            'Sítio Cozinha' => [
                ['da Cozinha', 'Sítio', '58228-000', 'Estrada rural']
            ],
            'Sítio Marias Pretas' => [
                ['das Marias Pretas', 'Sítio', '58228-000', 'Estrada de acesso']
            ],
            'Sítio Panelas' => [
                ['das Panelas', 'Sítio', '58228-000', 'Acesso rural']
            ],
            'Sítio Pinhões' => [
                ['dos Pinhões', 'Sítio', '58228-000', 'Estrada rural']
            ],
            'Sítio Raposa' => [
                ['da Raposa', 'Sítio', '58228-000', 'Estrada de acesso']
            ],
            'Sítio Salgado de Manoel Moreira' => [
                ['Salgado de Manoel Moreira', 'Sítio', '58228-000', 'Acesso rural']
            ],
            'Sítio Várzea Grande' => [
                ['da Várzea Grande', 'Sítio', '58228-000', 'Estrada rural']
            ],
            'Sítio Zé Paz de Baixo' => [
                ['Zé Paz de Baixo', 'Sítio', '58228-000', 'Acesso rural']
            ],
            'Sítio Zé Paz de Cima' => [
                ['Zé Paz de Cima', 'Sítio', '58228-000', 'Estrada de acesso']
            ],
            
            // Sítios - Área 03
            'Sítio Barbatão' => [
                ['do Barbatão', 'Sítio', '58228-000', 'Estrada rural']
            ],
            'Sítio Caboclo de Palhares' => [
                ['Caboclo de Palhares', 'Sítio', '58228-000', 'Acesso rural']
            ],
            'Sítio Chã de Palhares' => [
                ['Chã de Palhares', 'Sítio', '58228-000', 'Estrada de acesso']
            ],
            'Sítio Glória' => [
                ['da Glória', 'Sítio', '58228-000', 'Estrada rural']
            ],
            'Sítio Massaranduba' => [
                ['da Massaranduba', 'Sítio', '58228-000', 'Acesso rural']
            ],
            'Sítio Mata' => [
                ['da Mata', 'Sítio', '58228-000', 'Estrada rural']
            ],
            'Sítio Tanque do Veado' => [
                ['do Tanque do Veado', 'Sítio', '58228-000', 'Estrada de acesso']
            ],
            'Sítio Umari' => [
                ['do Umari', 'Sítio', '58228-000', 'Acesso rural']
            ],
            'Sítio Vaca Morta' => [
                ['da Vaca Morta', 'Sítio', '58228-000', 'Estrada rural']
            ],
            'Sítio Zé de Fogo' => [
                ['Zé de Fogo', 'Sítio', '58228-000', 'Estrada de acesso']
            ],
            'Sítio Zé Paz da Serra' => [
                ['Zé Paz da Serra', 'Sítio', '58228-000', 'Acesso rural']
            ],
            
            // Sítios - Área 04
            'Sítio Brejinho' => [
                ['Principal do Brejinho', 'Sítio', '58228-000', 'Estrada de acesso principal']
            ],
            'Sítio Caco' => [
                ['do Caco', 'Sítio', '58228-000', 'Estrada rural']
            ],
            'Sítio Canafistula' => [
                ['da Canafistula', 'Sítio', '58228-000', 'Acesso rural']
            ],
            'Sítio Cruz' => [
                ['da Cruz', 'Sítio', '58228-000', 'Estrada de acesso']
            ],
            'Sítio Lagoa do Braz' => [
                ['da Lagoa do Braz', 'Sítio', '58228-000', 'Estrada rural']
            ],
            'Sítio Pedra Lisa' => [
                ['da Pedra Lisa', 'Sítio', '58228-000', 'Acesso rural']
            ],
            'Sítio Pimenta' => [
                ['do Pimenta', 'Sítio', '58228-000', 'Estrada rural']
            ],
            'Sítio Pitomba' => [
                ['da Pitomba', 'Sítio', '58228-000', 'Estrada de acesso']
            ],
            'Sítio Raimundo' => [
                ['do Raimundo', 'Sítio', '58228-000', 'Acesso rural']
            ],
            'Sítio Seró' => [
                ['do Seró', 'Sítio', '58228-000', 'Estrada rural']
            ],
            'Sítio Tapuio' => [
                ['do Tapuio', 'Sítio', '58228-000', 'Estrada de acesso']
            ],
            'Sítio Umarizinho' => [
                ['do Umarizinho', 'Sítio', '58228-000', 'Acesso rural']
            ],
            
            // Sítios - Área 05
            'Sítio Barroção' => [
                ['do Barroção', 'Sítio', '58228-000', 'Estrada rural']
            ],
            'Sítio Cafundó' => [
                ['do Cafundó', 'Sítio', '58228-000', 'Acesso rural']
            ],
            'Sítio Caiana' => [
                ['da Caiana', 'Sítio', '58228-000', 'Estrada rural']
            ],
            'Sítio Cajazeiras' => [
                ['das Cajazeiras', 'Sítio', '58228-000', 'Estrada de acesso']
            ],
            'Sítio Lajedo Preto' => [
                ['do Lajedo Preto', 'Sítio', '58228-000', 'Acesso rural']
            ],
            'Sítio Marcação' => [
                ['da Marcação', 'Sítio', '58228-000', 'Estrada rural']
            ],
            'Sítio Mulungu' => [
                ['do Mulungu', 'Sítio', '58228-000', 'Estrada de acesso']
            ],
            'Sítio Pedra Lavrada' => [
                ['da Pedra Lavrada', 'Sítio', '58228-000', 'Acesso rural']
            ],
            'Sítio Queimadas' => [
                ['das Queimadas', 'Sítio', '58228-000', 'Estrada rural']
            ],
            'Sítio Salgadinho' => [
                ['do Salgadinho', 'Sítio', '58228-000', 'Estrada de acesso']
            ],
            'Sítio Seixos' => [
                ['dos Seixos', 'Sítio', '58228-000', 'Acesso rural']
            ],
            'Sítio Tanques' => [
                ['dos Tanques', 'Sítio', '58228-000', 'Estrada rural']
            ],
            
            // Sítios - Área 06
            'Sítio Boa Vista' => [
                ['da Boa Vista', 'Sítio', '58228-000', 'Estrada rural']
            ],
            'Sítio Carnaúba' => [
                ['da Carnaúba', 'Sítio', '58228-000', 'Acesso rural']
            ],
            'Sítio Carnaúbeira de Cima' => [
                ['da Carnaúbeira de Cima', 'Sítio', '58228-000', 'Estrada de acesso']
            ],
            'Sítio Estreito' => [
                ['do Estreito', 'Sítio', '58228-000', 'Estrada rural']
            ],
            'Sítio Lagoa da Serra' => [
                ['da Lagoa da Serra', 'Sítio', '58228-000', 'Acesso rural']
            ],
            'Sítio Mela Bode' => [
                ['do Mela Bode', 'Sítio', '58228-000', 'Estrada rural']
            ],
            'Sítio Oiticica' => [
                ['da Oiticica', 'Sítio', '58228-000', 'Estrada de acesso']
            ],
            'Sítio Olho D\'Água do Gregório' => [
                ['Olho D\'Água do Gregório', 'Sítio', '58228-000', 'Acesso rural']
            ],
            'Sítio Serra do Sítio' => [
                ['da Serra do Sítio', 'Sítio', '58228-000', 'Estrada rural']
            ],
            'Sítio Volta' => [
                ['da Volta', 'Sítio', '58228-000', 'Estrada de acesso']
            ]
        ];

        foreach ($sitiosComLogradouros as $nomeBairro => $logradouros) {
            if (isset($bairrosMap[$nomeBairro])) {
                $idBairro = $bairrosMap[$nomeBairro]['id_bairro'];
                foreach ($logradouros as $logradouro) {
                    $data[] = [
                        'nome_logradouro' => $logradouro[0],
                        'tipo_logradouro' => $logradouro[1],
                        'cep' => $logradouro[2],
                        'id_bairro' => $idBairro,
                        'observacoes' => $logradouro[3],
                        'created_at' => $currentDateTime,
                        'updated_at' => $currentDateTime
                    ];
                }
            }
        }

        if (!empty($data)) {
            // Inserir os dados em lotes
            $this->db->table('logradouros')->insertBatch($data);
            echo "Total de " . count($data) . " logradouros inseridos com sucesso!\n";
            echo "Logradouros criados para " . count(array_filter(array_keys($bairrosMap), function($nome) use ($data, $bairrosMap) {
                return in_array($bairrosMap[$nome]['id_bairro'], array_column($data, 'id_bairro'));
            })) . " bairros.\n";
        } else {
            echo "Nenhum logradouro foi inserido. Verifique se os bairros existem.\n";
        }
    }
}
