<!-- New User Modal -->
<div class="modal fade" id="newUserModal" tabindex="-1" aria-labelledby="newUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newUserModalLabel">
                    <i class="bi bi-person-plus"></i> Novo Usuário
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="newUserForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userName" class="form-label">Nome Completo *</label>
                                <input type="text" class="form-control" id="userName" name="nome" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userCPF" class="form-label">CPF *</label>
                                <input type="text" class="form-control" id="userCPF" name="cpf" 
                                       placeholder="000.000.000-00" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="userEmail" name="email">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userProfile" class="form-label">Perfil *</label>
                                <select class="form-select" id="userProfile" name="perfil" required>
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
                                <label for="userPassword" class="form-label">Senha Temporária *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="userPassword" name="senha" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-primary" type="button" id="generatePassword">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback"></div>
                                <div class="form-text">Mínimo 6 caracteres</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="userActive" name="ativo" checked>
                                    <label class="form-check-label" for="userActive">
                                        Usuário ativo
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="changePassword" name="forcar_alteracao" checked>
                                    <label class="form-check-label" for="changePassword">
                                        Forçar alteração de senha no primeiro login
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle"></i>
                        <strong>Informações importantes:</strong>
                        <ul class="mb-0 mt-2">
                            <li>O CPF será usado como nome de usuário para login</li>
                            <li>Se marcado "forçar alteração", o usuário deverá alterar a senha no primeiro acesso</li>
                            <li>O email é opcional, mas recomendado para recuperação de senha</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Criar Usuário
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
