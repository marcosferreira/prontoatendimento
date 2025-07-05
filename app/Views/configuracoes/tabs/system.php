<h4>Configurações Gerais</h4>

<form id="systemConfigForm">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6><i class="bi bi-hospital"></i> Informações da Unidade</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nome da Unidade</label>
                        <input type="text" class="form-control" name="unidade_nome" 
                               value="<?= esc($configuracoes['unidade']['unidade_nome'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CNPJ</label>
                        <input type="text" class="form-control" name="unidade_cnpj" 
                               value="<?= esc($configuracoes['unidade']['unidade_cnpj'] ?? '') ?>"
                               placeholder="00.000.000/0000-00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Endereço</label>
                        <input type="text" class="form-control" name="unidade_endereco" 
                               value="<?= esc($configuracoes['unidade']['unidade_endereco'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" class="form-control" name="unidade_telefone" 
                               value="<?= esc($configuracoes['unidade']['unidade_telefone'] ?? '') ?>"
                               placeholder="(00) 0000-0000">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6><i class="bi bi-sliders"></i> Parâmetros do Sistema</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Timeout de Sessão (minutos)</label>
                        <input type="number" class="form-control" name="sistema_timeout_sessao" 
                               value="<?= esc($configuracoes['sistema']['sistema_timeout_sessao'] ?? '60') ?>"
                               min="15" max="480">
                        <div class="form-text">Tempo limite de inatividade antes do logout automático</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tempo Limite Triagem (minutos)</label>
                        <input type="number" class="form-control" name="sistema_tempo_triagem" 
                               value="<?= esc($configuracoes['sistema']['sistema_tempo_triagem'] ?? '15') ?>"
                               min="5" max="60">
                        <div class="form-text">Tempo limite para completar a triagem</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Capacidade Máxima de Atendimento</label>
                        <input type="number" class="form-control" name="sistema_capacidade_maxima" 
                               value="<?= esc($configuracoes['sistema']['sistema_capacidade_maxima'] ?? '50') ?>"
                               min="10" max="500">
                        <div class="form-text">Número máximo de pacientes em atendimento simultâneo</div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="sistema_notificacoes_email" 
                               id="notifications" value="1"
                               <?= ($configuracoes['sistema']['sistema_notificacoes_email'] ?? false) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="notifications">
                            Notificações por Email
                        </label>
                        <div class="form-text">Enviar notificações importantes por email</div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h6><i class="bi bi-palette"></i> Aparência</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Tema</label>
                        <select class="form-select" name="aparencia_tema">
                            <option value="claro" <?= ($configuracoes['aparencia']['aparencia_tema'] ?? 'claro') === 'claro' ? 'selected' : '' ?>>Claro</option>
                            <option value="escuro" <?= ($configuracoes['aparencia']['aparencia_tema'] ?? 'claro') === 'escuro' ? 'selected' : '' ?>>Escuro</option>
                            <option value="automatico" <?= ($configuracoes['aparencia']['aparencia_tema'] ?? 'claro') === 'automatico' ? 'selected' : '' ?>>Automático</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cor Primária</label>
                        <input type="color" class="form-control form-control-color" name="aparencia_cor_primaria" 
                               value="<?= esc($configuracoes['aparencia']['aparencia_cor_primaria'] ?? '#1e3a8a') ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-end mt-3">
        <button type="button" class="btn btn-secondary me-2" id="resetConfigBtn">
            <i class="bi bi-arrow-clockwise"></i> Restaurar Padrões
        </button>
        <button type="submit" class="btn btn-success" id="saveConfigBtn">
            <i class="bi bi-check-circle"></i> Salvar Configurações
        </button>
    </div>
</form>
