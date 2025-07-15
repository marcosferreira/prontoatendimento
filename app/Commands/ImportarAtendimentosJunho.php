<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\AtendimentoModel;
use App\Models\PacienteModel;
use App\Models\MedicoModel;

class ImportarAtendimentosJunho extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'importar:atendimentos-junho';
    protected $description = 'Importa os dados do arquivo atendimento-junho.csv para as entidades do banco de dados.';

    public function run(array $params)
    {
        $csvPath = WRITEPATH . '../docs/database/atendimento-junho.csv';
        if (!file_exists($csvPath)) {
            CLI::error('Arquivo CSV não encontrado: ' . $csvPath);
            return;
        }

        CLI::write('Iniciando importação do CSV...', 'green');
        
        // Primeira passada: Coletar dados únicos
        $csvData = $this->parseCSV($csvPath);
        $uniqueData = $this->extractUniqueEntities($csvData);
        
        // Segunda passada: Inserir entidades no banco
        $entityIds = $this->insertEntities($uniqueData);
        
        // Terceira passada: Processar atendimentos
        $this->processAtendimentos($csvData, $entityIds);
        
        CLI::write("Importação concluída!", 'green');
    }
    
    private function parseCSV($csvPath)
    {
        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            CLI::error('Não foi possível abrir o arquivo CSV.');
            return [];
        }

        $header = fgetcsv($handle, 0, ';');
        
        // Remove BOM se presente no primeiro campo
        if (!empty($header[0])) {
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
        }
        
        $header = array_map(fn($h) => trim($h), $header);
        
        CLI::write("Cabeçalhos encontrados: " . implode(' | ', $header), 'cyan');
        
        $csvData = [];
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (count($row) === count($header)) {
                $data = array_combine($header, $row);
                $data = array_combine(array_map('trim', array_keys($data)), array_values($data));
                $csvData[] = $data;
            }
        }
        fclose($handle);
        
        CLI::write("Linhas do CSV carregadas: " . count($csvData), 'yellow');
        return $csvData;
    }
    
    private function extractUniqueEntities($csvData)
    {
        $pacientes = [];
        $medicos = [];
        $bairros = [];
        $logradouros = [];
        
        foreach ($csvData as $row) {
            // Extrair pacientes únicos
            $nomePaciente = trim($row['NOME'] ?? '');
            $susOuCpf = trim($row['SUS / CPF'] ?? '');
            
            // Pular linhas com dados vazios essenciais
            if (empty($nomePaciente) || empty($row['DATA DE NASCIMENTO'])) {
                continue;
            }
            
            // Lógica para diferenciar CPF de SUS
            $cpf = null;
            $sus = null;
            if ($susOuCpf && strtoupper($susOuCpf) !== 'NÃO' && is_numeric($susOuCpf)) {
                if (strlen($susOuCpf) == 15) {
                    $sus = $susOuCpf;
                } elseif (strlen($susOuCpf) == 11 || strlen($susOuCpf) == 14) {
                    $cpf = $susOuCpf;
                }
            }
            
            $dataNascimento = date_create_from_format('d/m/Y', trim($row['DATA DE NASCIMENTO']));
            $dataNascimentoStr = $dataNascimento ? $dataNascimento->format('Y-m-d') : null;
            
            $pacienteKey = $cpf ?: $sus ?: ($nomePaciente . '|' . $dataNascimentoStr);
            if (!isset($pacientes[$pacienteKey]) && !empty($nomePaciente)) {
                $pacientes[$pacienteKey] = [
                    'nome' => $nomePaciente,
                    'cpf' => $cpf,
                    'sus' => $sus,
                    'data_nascimento' => $dataNascimentoStr,
                    'idade' => !empty($row['IDADE']) ? (int)trim($row['IDADE']) : null,
                    'sexo' => $this->determinarSexo($nomePaciente), // Determinar sexo baseado no nome
                    'endereco' => trim($row['ENDEREÇO'] ?? ''),
                    'bairro' => trim($row['BAIRROS/ ÁREAS'] ?? '')
                ];
            }
            
            // Extrair médicos únicos
            $nomeMedico = trim($row['MÉDICO'] ?? '');
            if (!empty($nomeMedico) && !isset($medicos[$nomeMedico])) {
                // Gerar CRM fictício baseado no nome para evitar duplicatas
                $crmFicticio = 'IMP' . substr(md5($nomeMedico), 0, 7);
                $medicos[$nomeMedico] = [
                    'nome' => $nomeMedico,
                    'crm' => $crmFicticio,
                    'especialidade' => 'Medicina Geral',
                    'status' => 'Ativo'
                ];
            }
            
            // Extrair bairros únicos
            $nomeBairro = trim($row['BAIRROS/ ÁREAS'] ?? '');
            if (!empty($nomeBairro) && !isset($bairros[$nomeBairro])) {
                $bairros[$nomeBairro] = [
                    'nome_bairro' => $nomeBairro
                ];
            }
            
            // Extrair logradouros únicos
            $nomeLogradouro = trim($row['ENDEREÇO'] ?? '');
            if (!empty($nomeLogradouro) && !isset($logradouros[$nomeLogradouro])) {
                // Determinar tipo de logradouro baseado no prefixo
                $tipoLogradouro = $this->determinarTipoLogradouro($nomeLogradouro);
                $nomeLogradouroLimpo = $this->limparNomeLogradouro($nomeLogradouro, $tipoLogradouro);
                
                $logradouros[$nomeLogradouro] = [
                    'nome_logradouro' => $nomeLogradouroLimpo,
                    'tipo_logradouro' => $tipoLogradouro,
                    'cidade' => 'Dona Inês',
                    'estado' => 'PB',
                    'bairro' => $nomeBairro
                ];
            }
        }
        
        CLI::write("Entidades únicas encontradas:", 'yellow');
        CLI::write("- Pacientes: " . count($pacientes), 'cyan');
        CLI::write("- Médicos: " . count($medicos), 'cyan');
        CLI::write("- Bairros: " . count($bairros), 'cyan');
        CLI::write("- Logradouros: " . count($logradouros), 'cyan');
        
        return [
            'pacientes' => $pacientes,
            'medicos' => $medicos,
            'bairros' => $bairros,
            'logradouros' => $logradouros
        ];
    }
    
    private function insertEntities($uniqueData)
    {
        $bairroModel = new \App\Models\BairroModel();
        $logradouroModel = new \App\Models\LogradouroModel();
        $pacienteModel = new \App\Models\PacienteModel();
        $medicoModel = new \App\Models\MedicoModel();
        
        $entityIds = [
            'bairros' => [],
            'logradouros' => [],
            'pacientes' => [],
            'medicos' => []
        ];
        
        // Inserir bairros
        CLI::write("Inserindo bairros...", 'yellow');
        $bairrosInseridos = 0;
        foreach ($uniqueData['bairros'] as $key => $bairro) {
            // Verificar se já existe usando trim para evitar problemas de espaços
            $nomeNormalizado = trim($bairro['nome_bairro']);
            $existente = $bairroModel->where('TRIM(nome_bairro)', $nomeNormalizado)->first();
            if ($existente) {
                $entityIds['bairros'][$key] = $existente['id_bairro'];
            } else {
                $bairroData = [
                    'nome_bairro' => $nomeNormalizado
                ];
                $id = $bairroModel->insert($bairroData);
                if ($id) {
                    $entityIds['bairros'][$key] = $id;
                    $bairrosInseridos++;
                } else {
                    CLI::write("Erro ao inserir bairro: " . $nomeNormalizado, 'red');
                    CLI::write("Erros: " . print_r($bairroModel->errors(), true), 'red');
                }
            }
        }
        CLI::write("Bairros inseridos: $bairrosInseridos", 'cyan');
        
        // Inserir logradouros
        CLI::write("Inserindo logradouros...", 'yellow');
        $logradourosInseridos = 0;
        foreach ($uniqueData['logradouros'] as $key => $logradouro) {
            $idBairro = $entityIds['bairros'][$logradouro['bairro']] ?? null;
            
            // Verificar se já existe pelo nome e tipo limpos
            $existente = $logradouroModel->where('nome_logradouro', $logradouro['nome_logradouro'])
                                        ->where('tipo_logradouro', $logradouro['tipo_logradouro'])
                                        ->first();
            
            if ($existente) {
                $entityIds['logradouros'][$key] = $existente['id_logradouro'];
            } else {
                $logradouroData = [
                    'nome_logradouro' => $logradouro['nome_logradouro'],
                    'tipo_logradouro' => $logradouro['tipo_logradouro'],
                    'cidade' => $logradouro['cidade'],
                    'estado' => $logradouro['estado']
                ];
                
                // Adiciona bairro apenas se existir
                if ($idBairro) {
                    $logradouroData['id_bairro'] = $idBairro;
                } else {
                    // Se não tem bairro, criar um bairro genérico
                    $bairroGenerico = $bairroModel->where('nome_bairro', 'Outros')->first();
                    if (!$bairroGenerico) {
                        $idBairroGenerico = $bairroModel->insert(['nome_bairro' => 'Outros']);
                        $logradouroData['id_bairro'] = $idBairroGenerico;
                    } else {
                        $logradouroData['id_bairro'] = $bairroGenerico['id_bairro'];
                    }
                }
                
                try {
                    // Temporariamente desabilitar validação para permitir logradouros sem bairro
                    $logradouroModel->skipValidation();
                    $id = $logradouroModel->insert($logradouroData);
                    $logradouroModel->skipValidation(false);
                    
                    if ($id) {
                        $entityIds['logradouros'][$key] = $id;
                        $logradourosInseridos++;
                    } else {
                        CLI::write("Erro ao inserir logradouro: " . $logradouro['nome_logradouro'], 'red');
                    }
                } catch (\Exception $e) {
                    CLI::write("Erro ao inserir logradouro {$logradouro['nome_logradouro']}: " . $e->getMessage(), 'red');
                }
            }
        }
        CLI::write("Logradouros inseridos: $logradourosInseridos", 'cyan');
        
        // Inserir médicos
        CLI::write("Inserindo médicos...", 'yellow');
        $medicosInseridos = 0;
        foreach ($uniqueData['medicos'] as $key => $medico) {
            // Verificar se já existe por nome ou CRM
            $existente = $medicoModel->where('nome', $medico['nome'])
                                    ->orWhere('crm', $medico['crm'])
                                    ->first();
            if ($existente) {
                $entityIds['medicos'][$key] = $existente['id_medico'];
            } else {
                try {
                    // Temporariamente desabilitar validação para permitir CRMs fictícios
                    $medicoModel->skipValidation();
                    $id = $medicoModel->insert($medico);
                    $medicoModel->skipValidation(false);
                    
                    if ($id) {
                        $entityIds['medicos'][$key] = $id;
                        $medicosInseridos++;
                    } else {
                        CLI::write("Erro ao inserir médico: " . $medico['nome'], 'red');
                        CLI::write("Erros: " . print_r($medicoModel->errors(), true), 'red');
                    }
                } catch (\Exception $e) {
                    CLI::write("Exceção ao inserir médico {$medico['nome']}: " . $e->getMessage(), 'red');
                }
            }
        }
        CLI::write("Médicos inseridos: $medicosInseridos", 'cyan');
        
        // Inserir pacientes
        CLI::write("Inserindo pacientes...", 'yellow');
        $pacientesInseridos = 0;
        foreach ($uniqueData['pacientes'] as $key => $paciente) {
            $idLogradouro = null;
            
            // Buscar o ID do logradouro pelo nome
            if (!empty($paciente['endereco']) && isset($entityIds['logradouros'][$paciente['endereco']])) {
                $idLogradouro = $entityIds['logradouros'][$paciente['endereco']];
            }
            
            $existente = null;
            if ($paciente['cpf']) {
                $existente = $pacienteModel->where('cpf', $paciente['cpf'])->first();
            } elseif ($paciente['sus']) {
                $existente = $pacienteModel->where('sus', $paciente['sus'])->first();
            } else {
                $existente = $pacienteModel->where('nome', $paciente['nome'])
                    ->where('data_nascimento', $paciente['data_nascimento'])
                    ->first();
            }
            
            if ($existente) {
                $entityIds['pacientes'][$key] = $existente['id_paciente'];
            } else {
                $pacienteData = [
                    'nome' => $paciente['nome'],
                    'data_nascimento' => $paciente['data_nascimento'],
                    'sexo' => $paciente['sexo'], // Campo obrigatório
                    'observacoes' => 'Importado via CSV'
                ];
                
                // Adiciona idade apenas se não for null
                if ($paciente['idade'] !== null) {
                    $pacienteData['idade'] = $paciente['idade'];
                }
                
                // Adiciona CPF ou SUS apenas se não for null
                if ($paciente['cpf']) {
                    $pacienteData['cpf'] = $paciente['cpf'];
                }
                if ($paciente['sus']) {
                    $pacienteData['sus'] = $paciente['sus'];
                }
                
                // Adiciona logradouro apenas se existir e for válido
                if ($idLogradouro) {
                    $pacienteData['id_logradouro'] = $idLogradouro;
                }
                
                try {
                    // Paciente model já tem skipValidation = true
                    $id = $pacienteModel->insert($pacienteData);
                    
                    if ($id) {
                        $entityIds['pacientes'][$key] = $id;
                        $pacientesInseridos++;
                    } else {
                        CLI::write("Erro ao inserir paciente: " . $paciente['nome'], 'red');
                        CLI::write("Erros: " . print_r($pacienteModel->errors(), true), 'red');
                    }
                } catch (\Exception $e) {
                    CLI::write("Exceção ao inserir paciente {$paciente['nome']}: " . $e->getMessage(), 'red');
                }
            }
        }
        CLI::write("Pacientes inseridos: $pacientesInseridos", 'cyan');
        
        return $entityIds;
    }
    
    private function processAtendimentos($csvData, $entityIds)
    {
        $atendimentoModel = new \App\Models\AtendimentoModel();
        $count = 0;
        
        CLI::write("Processando atendimentos...", 'yellow');
        
        foreach ($csvData as $row) {
            // Identificar paciente e médico
            $nomePaciente = trim($row['NOME'] ?? '');
            $susOuCpf = trim($row['SUS / CPF'] ?? '');
            
            // Pular linhas com dados vazios essenciais
            if (empty($nomePaciente) || empty($row['DATA DE NASCIMENTO']) || empty($row['MÉDICO'])) {
                continue;
            }
            
            // Lógica para diferenciar CPF de SUS
            $cpf = null;
            $sus = null;
            if ($susOuCpf && strtoupper($susOuCpf) !== 'NÃO' && is_numeric($susOuCpf)) {
                if (strlen($susOuCpf) == 15) {
                    $sus = $susOuCpf;
                } elseif (strlen($susOuCpf) == 11 || strlen($susOuCpf) == 14) {
                    $cpf = $susOuCpf;
                }
            }
            
            $dataNascimento = date_create_from_format('d/m/Y', trim($row['DATA DE NASCIMENTO']));
            $dataNascimentoStr = $dataNascimento ? $dataNascimento->format('Y-m-d') : null;
            $nomeMedico = trim($row['MÉDICO']);
            
            $pacienteKey = $cpf ?: $sus ?: ($nomePaciente . '|' . $dataNascimentoStr);
            $idPaciente = $entityIds['pacientes'][$pacienteKey] ?? null;
            $idMedico = $entityIds['medicos'][$nomeMedico] ?? null;
            
            if (!$idPaciente || !$idMedico) {
                CLI::write("Erro: Paciente ($nomePaciente) ou médico ($nomeMedico) não encontrado para linha " . ($count + 1), 'red');
                continue;
            }
            
            // Processar atendimento
            $dataAtendimento = date_create_from_format('d/m/Y', trim($row['DATA DO ATENDIMENTO']));
            if (!$dataAtendimento) {
                $dataAtendimento = new \DateTime(); // Data atual se não conseguir fazer o parse
            }
            $dataAtendimentoStr = $dataAtendimento->format('Y-m-d H:i:s');
            
            // Tratar classificação de risco
            $classificacaoRisco = trim($row['CLASSIFICAÇÃO DE RISCO'] ?? '');
            if (!empty($classificacaoRisco) && strtoupper($classificacaoRisco) !== 'NÃO') {
                $classificacaoRisco = ucfirst(strtolower($classificacaoRisco));
            } else {
                $classificacaoRisco = 'Verde'; // Padrão
            }
            
            $atendimentoData = [
                'id_paciente' => $idPaciente,
                'id_medico' => $idMedico,
                'data_atendimento' => $dataAtendimentoStr,
                'classificacao_risco' => $classificacaoRisco,
                'consulta_enfermagem' => strtoupper(trim($row['CONSULTA DE ENFERMAGEM'] ?? '')) === 'SIM' ? 1 : 0,
                'status' => 'Finalizado',
            ];
            
            // Campos opcionais
            $hgtGlicemia = trim($row['HGT - GLICEMIA'] ?? '');
            if (!empty($hgtGlicemia) && strtoupper($hgtGlicemia) !== 'NÃO' && is_numeric($hgtGlicemia)) {
                $atendimentoData['hgt_glicemia'] = (float)$hgtGlicemia;
            }
            
            $pressaoArterial = trim($row['PRESSÃO ARTERIAL SISTÊMICA'] ?? '');
            if (!empty($pressaoArterial) && strtoupper($pressaoArterial) !== 'NÃO') {
                $atendimentoData['pressao_arterial'] = $pressaoArterial;
            }
            
            $hipoteseDiagnostico = trim($row['HIPÓTESE/ DIAGNÓSTICO'] ?? '');
            if (!empty($hipoteseDiagnostico) && strtoupper($hipoteseDiagnostico) !== 'NÃO') {
                $atendimentoData['hipotese_diagnostico'] = $hipoteseDiagnostico;
            }
            
            $observacao = trim($row['OBSERVAÇÃO'] ?? '');
            if (!empty($observacao) && strtoupper($observacao) !== 'NÃO') {
                $atendimentoData['observacao'] = $observacao;
            }
            
            $encaminhamento = trim($row['ENCAMINHAMENTO'] ?? '');
            if (!empty($encaminhamento) && strtoupper($encaminhamento) !== 'NÃO') {
                $atendimentoData['encaminhamento'] = ucfirst(strtolower($encaminhamento));
            }
            
            $atendimentoData['obito'] = strtoupper(trim($row['ÓBITO'] ?? '')) === 'SIM' ? 1 : 0;
            
            try {
                $atendimentoModel->skipValidation();
                $atendimentoModel->insert($atendimentoData);
                $atendimentoModel->skipValidation(false);
                $count++;
            } catch (\Exception $e) {
                CLI::write("Erro ao inserir atendimento para {$nomePaciente}: " . $e->getMessage(), 'red');
            }
        }
        
        CLI::write("Atendimentos processados: $count", 'green');
    }
    
    /**
     * Determina o tipo de logradouro baseado no prefixo do nome
     */
    private function determinarTipoLogradouro($nomeLogradouro)
    {
        $nome = strtoupper(trim($nomeLogradouro));
        
        if (strpos($nome, 'RUA ') === 0 || strpos($nome, 'R. ') === 0) {
            return 'Rua';
        } elseif (strpos($nome, 'AVENIDA ') === 0 || strpos($nome, 'AV. ') === 0) {
            return 'Avenida';
        } elseif (strpos($nome, 'TRAVESSA ') === 0 || strpos($nome, 'TV. ') === 0) {
            return 'Travessa';
        } elseif (strpos($nome, 'ALAMEDA ') === 0 || strpos($nome, 'AL. ') === 0) {
            return 'Alameda';
        } elseif (strpos($nome, 'PRAÇA ') === 0 || strpos($nome, 'PC. ') === 0) {
            return 'Praça';
        } elseif (strpos($nome, 'ESTRADA ') === 0 || strpos($nome, 'EST. ') === 0) {
            return 'Estrada';
        } elseif (strpos($nome, 'SÍTIO ') === 0 || strpos($nome, 'ST ') === 0) {
            return 'Sítio';
        } elseif (strpos($nome, 'RODOVIA ') === 0 || strpos($nome, 'ROD. ') === 0) {
            return 'Rodovia';
        } elseif (strpos($nome, 'VIA ') === 0) {
            return 'Via';
        } elseif (strpos($nome, 'BECO ') === 0) {
            return 'Beco';
        } elseif (strpos($nome, 'LARGO ') === 0) {
            return 'Largo';
        } else {
            // Se não conseguir determinar, assumir como Rua
            return 'Rua';
        }
    }

    /**
     * Limpa e padroniza nome de logradouro removendo prefixos
     */
    private function limparNomeLogradouro($nomeCompleto, $tipo)
    {
        $nome = trim($nomeCompleto);
        
        // Remove prefixos conhecidos
        $prefixos = [
            'RUA ', 'R. ', 'AVENIDA ', 'AV. ', 'TRAVESSA ', 'TV. ',
            'ALAMEDA ', 'AL. ', 'PRAÇA ', 'PC. ', 'ESTRADA ', 'EST. ',
            'SÍTIO ', 'ST ', 'RODOVIA ', 'ROD. ', 'VIA ', 'BECO ', 'LARGO '
        ];
        
        foreach ($prefixos as $prefixo) {
            if (strpos(strtoupper($nome), $prefixo) === 0) {
                $nome = trim(substr($nome, strlen($prefixo)));
                break;
            }
        }
        
        return $nome;
    }
    
    /**
     * Tenta determinar o sexo baseado no nome do paciente
     */
    private function determinarSexo($nome)
    {
        $nome = strtolower(trim($nome));
        
        // Nomes tipicamente femininos
        $nomesFemininos = [
            'maria', 'ana', 'antonia', 'francisca', 'adriana', 'juliana', 'patricia', 'rosa', 'sandra', 'cristina',
            'fernanda', 'luciana', 'marcia', 'claudia', 'angela', 'monica', 'rita', 'vera', 'lucia', 'tereza',
            'regina', 'fatima', 'sonia', 'beatriz', 'elizabeth', 'helena', 'gloria', 'simone', 'denise', 'valeria'
        ];
        
        // Nomes tipicamente masculinos
        $nomesMasculinos = [
            'jose', 'antonio', 'francisco', 'carlos', 'paulo', 'pedro', 'lucas', 'luis', 'marcos', 'manuel',
            'joao', 'fernando', 'rafael', 'gustavo', 'eduardo', 'roberto', 'ricardo', 'sergio', 'bruno', 'daniel'
        ];
        
        // Separar o primeiro nome
        $primeiroNome = explode(' ', $nome)[0];
        
        // Verificar se está nas listas
        if (in_array($primeiroNome, $nomesFemininos)) {
            return 'F';
        } elseif (in_array($primeiroNome, $nomesMasculinos)) {
            return 'M';
        }
        
        // Se terminar com 'a', provavelmente feminino
        if (substr($primeiroNome, -1) === 'a' && strlen($primeiroNome) > 2) {
            return 'F';
        }
        
        // Caso padrão: masculino (pode ser ajustado)
        return 'M';
    }
}
