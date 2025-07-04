<?php

/**
 * Helper para Soft Delete
 * 
 * Funções utilitárias para trabalhar com soft delete no sistema
 */

if (!function_exists('restore_record')) {
    /**
     * Restaura um registro excluído (soft delete)
     *
     * @param string $modelName Nome do model
     * @param int $id ID do registro
     * @return bool
     */
    function restore_record(string $modelName, int $id): bool
    {
        $model = model($modelName);
        return $model->update($id, ['deleted_at' => null]);
    }
}

if (!function_exists('force_delete_record')) {
    /**
     * Exclui definitivamente um registro
     *
     * @param string $modelName Nome do model
     * @param int $id ID do registro
     * @return bool
     */
    function force_delete_record(string $modelName, int $id): bool
    {
        $model = model($modelName);
        return $model->delete($id, true); // true para hard delete
    }
}

if (!function_exists('get_deleted_records')) {
    /**
     * Busca registros excluídos de um model
     *
     * @param string $modelName Nome do model
     * @param int|null $limit Limite de registros
     * @return array
     */
    function get_deleted_records(string $modelName, ?int $limit = null): array
    {
        $model = model($modelName);
        $query = $model->onlyDeleted();
        
        if ($limit) {
            $query = $query->limit($limit);
        }
        
        return $query->findAll();
    }
}

if (!function_exists('count_deleted_records')) {
    /**
     * Conta registros excluídos de um model
     *
     * @param string $modelName Nome do model
     * @return int
     */
    function count_deleted_records(string $modelName): int
    {
        $model = model($modelName);
        return $model->onlyDeleted()->countAllResults();
    }
}

if (!function_exists('get_soft_delete_stats')) {
    /**
     * Retorna estatísticas de soft delete do sistema
     *
     * @return array
     */
    function get_soft_delete_stats(): array
    {
        $models = [
            'AtendimentoModel' => 'Atendimentos',
            'AtendimentoExameModel' => 'Atendimento Exames',
            'AtendimentoProcedimentoModel' => 'Atendimento Procedimentos',
            'BairroModel' => 'Bairros',
            'ExameModel' => 'Exames',
            'LogradouroModel' => 'Logradouros',
            'MedicoModel' => 'Médicos',
            'PacienteModel' => 'Pacientes',
            'ProcedimentoModel' => 'Procedimentos'
        ];

        $stats = [];
        
        foreach ($models as $modelName => $displayName) {
            try {
                $model = model($modelName);
                $stats[$displayName] = [
                    'total' => $model->withDeleted()->countAllResults(),
                    'ativos' => $model->countAllResults(),
                    'excluidos' => $model->onlyDeleted()->countAllResults()
                ];
            } catch (\Exception $e) {
                $stats[$displayName] = [
                    'total' => 0,
                    'ativos' => 0,
                    'excluidos' => 0,
                    'erro' => $e->getMessage()
                ];
            }
        }

        return $stats;
    }
}

if (!function_exists('bulk_restore_records')) {
    /**
     * Restaura múltiplos registros em lote
     *
     * @param string $modelName Nome do model
     * @param array $ids Array de IDs para restaurar
     * @return int Número de registros restaurados
     */
    function bulk_restore_records(string $modelName, array $ids): int
    {
        $model = model($modelName);
        $restored = 0;
        
        foreach ($ids as $id) {
            if ($model->update($id, ['deleted_at' => null])) {
                $restored++;
            }
        }
        
        return $restored;
    }
}

if (!function_exists('cleanup_old_deleted_records')) {
    /**
     * Remove definitivamente registros excluídos há mais de X dias
     *
     * @param string $modelName Nome do model
     * @param int $days Dias desde a exclusão
     * @return int Número de registros removidos
     */
    function cleanup_old_deleted_records(string $modelName, int $days = 30): int
    {
        $model = model($modelName);
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $oldDeletedRecords = $model->onlyDeleted()
            ->where('deleted_at <', $cutoffDate)
            ->findAll();
        
        $deleted = 0;
        foreach ($oldDeletedRecords as $record) {
            if ($model->delete($record[$model->primaryKey], true)) {
                $deleted++;
            }
        }
        
        return $deleted;
    }
}

if (!function_exists('is_record_deleted')) {
    /**
     * Verifica se um registro específico está excluído
     *
     * @param string $modelName Nome do model
     * @param int $id ID do registro
     * @return bool
     */
    function is_record_deleted(string $modelName, int $id): bool
    {
        $model = model($modelName);
        $record = $model->withDeleted()->find($id);
        
        return $record && !empty($record['deleted_at']);
    }
}
