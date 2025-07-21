<?php
namespace App\Validation;

use CodeIgniter\Shield\Models\UserModel;

class CustomRules
{
    /**
     * Valida se o usuário existe e pertence ao grupo 'medico'.
     * Permite vazio (relacionamento opcional).
     */
    public function validateUserIsMedicoGroup($str, string $fields, array $data): bool
    {
        if (empty($str)) return true;
        $user = model(UserModel::class)->find($str);
        if (!$user) return false;
        return $user->inGroup('medico');
    }
}
