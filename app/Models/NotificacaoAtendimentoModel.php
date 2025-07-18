<?php
namespace App\Models;

use CodeIgniter\Model;

class NotificacaoAtendimentoModel extends Model
{
    protected $table = 'notificacao_atendimentos';
    protected $primaryKey = 'id_notificacao_atendimento';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_notificacao',
        'id_atendimento',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'id_notificacao' => 'required|is_natural_no_zero',
        'id_atendimento' => 'required|is_natural_no_zero',
    ];

    // Busca todos os atendimentos vinculados a uma notificação
    public function getAtendimentosPorNotificacao($idNotificacao)
    {
        return $this->where('id_notificacao', $idNotificacao)->findAll();
    }

    // Busca todas as notificações vinculadas a um atendimento
    public function getNotificacoesPorAtendimento($idAtendimento)
    {
        return $this->where('id_atendimento', $idAtendimento)->findAll();
    }

    // Verifica se já existe vínculo para evitar duplicidade
    public function existeVinculo($idNotificacao, $idAtendimento)
    {
        return $this->where([
            'id_notificacao' => $idNotificacao,
            'id_atendimento' => $idAtendimento
        ])->first() !== null;
    }
}
