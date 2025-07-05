<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">
                    <i class="bi bi-pencil"></i> Editar Usuário
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm">
                <div class="modal-body">
                    <input type="hidden" id="editUserId" name="user_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editUserName" class="form-label">Nome Completo *</label>
                                <input type="text" class="form-control" id="editUserName" name="nome" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editUserCPF" class="form-label">CPF *</label>
                                <input type="text" class="form-control" id="editUserCPF" name="cpf" 
                                       placeholder="000.000.000-00" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editUserEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="editUserEmail" name="email">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editUserProfile" class="form-label">Perfil *</label>
                                <select class="form-select" id="editUserProfile" name="perfil" required>
                                    <option value="">Selecione...</option>
                                    <option value="admin">Administrador</option>
                                    <option value="medico">Médico</option>
                                    <option value="enfermeiro">Enfermeiro</option>
                                    <option value="farmaceutico">Farmacêutico</option>
                                    <option value="recepcionista">Recepcionista</option>
                                    <option value="gestor">Gestor</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status do Usuário</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="editUserActive" name="ativo">
                                    <label class="form-check-label" for="editUserActive">
                                        Usuário ativo
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Informações de Acesso</label>
                                <div class="text-muted small">
                                    <p><strong>Último acesso:</strong> <span id="editUserLastAccess">-</span></p>
                                    <p><strong>Criado em:</strong> <span id="editUserCreatedAt">-</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Atenção:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Alterações no perfil do usuário afetarão suas permissões no sistema</li>
                            <li>Para alterar a senha, use o botão "Resetar Senha" na lista de usuários</li>
                            <li>Desativar um usuário impedirá seu acesso ao sistema</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
