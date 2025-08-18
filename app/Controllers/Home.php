<?php

namespace App\Controllers;

use App\Models\PacienteModel;
use App\Models\AtendimentoModel;
use App\Models\MedicoModel;
use App\Models\BairroModel;
use App\Models\NotificacaoModel;

class Home extends BaseController
{
    protected $pacienteModel;
    protected $atendimentoModel;
    protected $medicoModel;
    protected $bairroModel;
    protected $notificacaoModel;

    public function __construct()
    {
        $this->pacienteModel = new PacienteModel();
        $this->atendimentoModel = new AtendimentoModel();
        $this->medicoModel = new MedicoModel();
        $this->bairroModel = new BairroModel();
        $this->notificacaoModel = new NotificacaoModel();
        
        // Carregar helper do dashboard
        helper('dashboard');
    }

    public function index(): string
    {
        // Estatísticas gerais do sistema
        $stats = $this->getDashboardStats();
        
        // Últimos atendimentos (5 mais recentes)
        $ultimosAtendimentos = $this->getUltimosAtendimentos();
        
        // Notificações ativas do sistema
        $notificacoes = $this->getNotificacoesAtivas();
        
        // Médicos ativos hoje
        $medicosAtivos = $this->getMedicosAtivosHoje();

        $data = [
            'title' => 'Painel de Controle',
            'description' => 'Monitoramento em Tempo Real | Pronto Atendimento Municipal',
            'keywords' => 'dashboard, pronto atendimento, SisPAM',
            'stats' => $stats,
            'ultimosAtendimentos' => $ultimosAtendimentos,
            'notificacoes' => $notificacoes,
            'medicosAtivos' => $medicosAtivos
        ];
        return view('index', $data);
    }

    /**
     * Busca estatísticas principais para o dashboard
     */
    private function getDashboardStats(): array
    {
        $hoje = date('Y-m-d');
        $mesAtual = date('Y-m');
        
        try {
            return [
                // Pacientes
                'total_pacientes' => $this->pacienteModel->countAllResults(),
                'pacientes_hoje' => $this->pacienteModel->where('DATE(created_at)', $hoje)->countAllResults(),
                'pacientes_mes' => $this->pacienteModel->where('DATE_FORMAT(created_at, "%Y-%m")', $mesAtual)->countAllResults(),
                
                // Atendimentos
                'total_atendimentos' => $this->atendimentoModel->countAllResults(),
                'atendimentos_hoje' => $this->atendimentoModel->where('DATE(data_atendimento)', $hoje)->countAllResults(),
                'atendimentos_mes' => $this->atendimentoModel->where('DATE_FORMAT(data_atendimento, "%Y-%m")', $mesAtual)->countAllResults(),
                'atendimentos_em_andamento' => $this->atendimentoModel->where('atendimentos.status', 'Em Andamento')->countAllResults(),
                
                // Classificação de Risco (hoje)
                'casos_vermelhos_hoje' => $this->atendimentoModel->where('DATE(data_atendimento)', $hoje)
                                                                 ->where('classificacao_risco', 'Vermelho')
                                                                 ->countAllResults(),
                'casos_laranjas_hoje' => $this->atendimentoModel->where('DATE(data_atendimento)', $hoje)
                                                               ->where('classificacao_risco', 'Laranja')
                                                               ->countAllResults(),
                'casos_amarelos_hoje' => $this->atendimentoModel->where('DATE(data_atendimento)', $hoje)
                                                                ->where('classificacao_risco', 'Amarelo')
                                                                ->countAllResults(),
                'casos_verdes_hoje' => $this->atendimentoModel->where('DATE(data_atendimento)', $hoje)
                                                              ->where('classificacao_risco', 'Verde')
                                                              ->countAllResults(),
                'casos_azuis_hoje' => $this->atendimentoModel->where('DATE(data_atendimento)', $hoje)
                                                             ->where('classificacao_risco', 'Azul')
                                                             ->countAllResults(),
                'casos_sem_classificacao_hoje' => $this->atendimentoModel->where('DATE(data_atendimento)', $hoje)
                                                                        ->where('classificacao_risco', 'Sem classificação')
                                                                        ->countAllResults(),
                
                // Médicos
                'total_medicos' => $this->medicoModel->countAllResults(),
                'medicos_ativos' => $this->medicoModel->where('medicos.status', 'Ativo')->countAllResults(),
                
                // Bairros
                'total_bairros' => $this->bairroModel->countAllResults(),
                
                // Idade média dos pacientes
                'idade_media' => $this->getIdadeMediaPacientes(),
                
                // Notificações
                'notificacoes_ativas' => $this->notificacaoModel->where('notificacoes.status', 'ativa')->countAllResults(),
                'notificacoes_criticas' => $this->notificacaoModel->where('notificacoes.status', 'ativa')
                                                                  ->where('severidade', 'critica')
                                                                  ->countAllResults()
            ];
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar estatísticas do dashboard: ' . $e->getMessage());
            return $this->getDefaultStats();
        }
    }

    /**
     * Busca os últimos 5 atendimentos
     */
    private function getUltimosAtendimentos(): array
    {
        try {
            $query = $this->atendimentoModel
                ->select('atendimentos.*, p.nome as paciente_nome, m.nome as medico_nome')
                ->join('pacientes p', 'p.id_paciente = atendimentos.id_paciente', 'left')
                ->join('medicos m', 'm.id_medico = atendimentos.id_medico', 'left')
                ->where('atendimentos.deleted_at IS NULL')
                ->orderBy('atendimentos.data_atendimento', 'DESC')
                ->limit(5)
                ->findAll();
            
            return $query ?: [];
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar últimos atendimentos: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca notificações ativas do sistema
     */
    private function getNotificacoesAtivas(): array
    {
        return $this->notificacaoModel->where('notificacoes.status', 'ativa')
                                     ->orderBy('severidade', 'DESC')
                                     ->orderBy('created_at', 'DESC')
                                     ->limit(3)
                                     ->findAll();
    }

    /**
     * Busca médicos que realizaram atendimentos hoje
     */
    private function getMedicosAtivosHoje(): array
    {
        $hoje = date('Y-m-d');
        
        return $this->medicoModel->select('medicos.*, COUNT(a.id_atendimento) as atendimentos_hoje')
                                ->join('atendimentos a', 'a.id_medico = medicos.id_medico')
                                ->where('DATE(a.data_atendimento)', $hoje)
                                ->where('medicos.status', 'Ativo')
                                ->groupBy('medicos.id_medico')
                                ->orderBy('atendimentos_hoje', 'DESC')
                                ->limit(5)
                                ->findAll();
    }

    /**
     * Calcula a idade média dos pacientes
     */
    private function getIdadeMediaPacientes(): float
    {
        try {
            $result = $this->pacienteModel->selectAvg('idade')->first();
            return round($result['idade'] ?? 0, 1);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao calcular idade média: ' . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Método de fallback para dados seguros mesmo sem registros
     */
    private function getDefaultStats(): array
    {
        return [
            'total_pacientes' => 0,
            'pacientes_hoje' => 0,
            'pacientes_mes' => 0,
            'total_atendimentos' => 0,
            'atendimentos_hoje' => 0,
            'atendimentos_mes' => 0,
            'atendimentos_em_andamento' => 0,
            'casos_vermelhos_hoje' => 0,
            'casos_laranjas_hoje' => 0,
            'casos_amarelos_hoje' => 0,
            'casos_verdes_hoje' => 0,
            'casos_azuis_hoje' => 0,
            'casos_sem_classificacao_hoje' => 0,
            'total_medicos' => 0,
            'medicos_ativos' => 0,
            'total_bairros' => 0,
            'idade_media' => 0.0,
            'notificacoes_ativas' => 0,
            'notificacoes_criticas' => 0
        ];
    }

    /**
     * Método para testar o envio de email
     * Acesse via: /test-email
     */
    public function testEmail()
    {
        $email = \Config\Services::email();
        
        $email->setTo('teste@exemplo.com');
        $email->setSubject('Teste de Email - Sistema Pronto Atendimento');
        $email->setMessage('Este é um email de teste para verificar se o sistema de email está funcionando corretamente.');
        
        if ($email->send()) {
            return 'Email enviado com sucesso! (Verifique os logs em writable/logs/emails/)';
        } else {
            return 'Falha ao enviar email. Erro: ' . $email->printDebugger();
        }
    }

    /**
     * Função de teste para debugar a busca de atendimentos
     */
    public function testAtendimentos()
    {
        // Verificar se as tabelas existem e têm dados
        echo "<h2>Teste de Busca de Atendimentos</h2>";
        
        // Teste 1: Buscar atendimentos simples
        echo "<h3>1. Busca simples de atendimentos:</h3>";
        $atendimentosSimples = $this->atendimentoModel->findAll();
        echo "Total de atendimentos encontrados: " . count($atendimentosSimples) . "<br>";
        echo "<pre>";
        print_r(array_slice($atendimentosSimples, 0, 2)); // Mostra apenas os 2 primeiros
        echo "</pre>";
        
        // Teste 2: Buscar pacientes
        echo "<h3>2. Busca de pacientes:</h3>";
        $pacientes = $this->pacienteModel->findAll();
        echo "Total de pacientes encontrados: " . count($pacientes) . "<br>";
        
        // Teste 3: Buscar médicos
        echo "<h3>3. Busca de médicos:</h3>";
        $medicos = $this->medicoModel->findAll();
        echo "Total de médicos encontrados: " . count($medicos) . "<br>";
        
        // Teste 4: Query com JOIN manual
        echo "<h3>4. Query com JOIN manual:</h3>";
        try {
            $db = \Config\Database::connect();
            $sql = "SELECT a.id_atendimento, a.data_atendimento, p.nome as paciente_nome, m.nome as medico_nome 
                    FROM pam_atendimentos a 
                    LEFT JOIN pam_pacientes p ON p.id_paciente = a.id_paciente 
                    LEFT JOIN pam_medicos m ON m.id_medico = a.id_medico 
                    ORDER BY a.data_atendimento DESC 
                    LIMIT 5";
            $result = $db->query($sql)->getResultArray();
            echo "Resultado SQL manual: " . count($result) . " registros<br>";
            echo "<pre>";
            print_r($result);
            echo "</pre>";
        } catch (\Exception $e) {
            echo "Erro na query manual: " . $e->getMessage() . "<br>";
        }
        
        // Teste 5: Query usando o Query Builder do CodeIgniter
        echo "<h3>5. Query com Query Builder:</h3>";
        try {
            $query = $this->atendimentoModel
                ->select('atendimentos.id_atendimento, atendimentos.data_atendimento, pacientes.nome as paciente_nome, medicos.nome as medico_nome')
                ->join('pacientes', 'pacientes.id_paciente = atendimentos.id_paciente', 'left')
                ->join('medicos', 'medicos.id_medico = atendimentos.id_medico', 'left')
                ->orderBy('atendimentos.data_atendimento', 'DESC')
                ->limit(5)
                ->findAll();
            
            echo "Resultado Query Builder: " . count($query) . " registros<br>";
            echo "<pre>";
            print_r($query);
            echo "</pre>";
        } catch (\Exception $e) {
            echo "Erro no Query Builder: " . $e->getMessage() . "<br>";
        }
        
        // Teste 6: Chamar a função original
        echo "<h3>6. Chamando getUltimosAtendimentos():</h3>";
        $ultimosAtendimentos = $this->getUltimosAtendimentos();
        echo "Resultado getUltimosAtendimentos: " . count($ultimosAtendimentos) . " registros<br>";
        echo "<pre>";
        print_r($ultimosAtendimentos);
        echo "</pre>";
    }
}
