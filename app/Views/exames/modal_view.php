<div class="row">
    <div class="col-12">
        <dl class="row">
            <dt class="col-sm-3">Nome</dt>
            <dd class="col-sm-9"><?= esc($exame['nome']) ?></dd>
            
            <dt class="col-sm-3">Código</dt>
            <dd class="col-sm-9"><?= esc($exame['codigo'] ?? '-') ?></dd>
            
            <dt class="col-sm-3">Tipo</dt>
            <dd class="col-sm-9"><?= ucfirst($exame['tipo']) ?></dd>
            
            <dt class="col-sm-3">Descrição</dt>
            <dd class="col-sm-9"><?= esc($exame['descricao'] ?? '-') ?></dd>
            
            <dt class="col-sm-3">Criado em</dt>
            <dd class="col-sm-9"><?= date('d/m/Y H:i', strtotime($exame['created_at'])) ?></dd>
        </dl>
    </div>
</div>

<div class="d-flex justify-content-end mt-3">
    <a href="<?= base_url('exames/' . $exame['id_exame'] . '/edit') ?>" class="btn btn-warning btn-sm me-2">
        <i class="bi bi-pencil"></i> Editar
    </a>
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>
</div>
