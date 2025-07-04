# Soft Delete - Referência Rápida

## Comandos CLI

### Estatísticas
```bash
php spark softdelete:manage stats
```

### Limpeza
```bash
# Padrão (30 dias)
php spark softdelete:manage cleanup

# Personalizada
php spark softdelete:manage cleanup --days=60

# Sem confirmação
php spark softdelete:manage cleanup --days=90 --force
```

### Restauração
```bash
php spark softdelete:manage restore --model=PacienteModel --id=123
```

## Código PHP

### Consultas Básicas
```php
// Apenas registros ativos (padrão)
$pacientes = $pacienteModel->findAll();

// Incluir excluídos
$todos = $pacienteModel->withDeleted()->findAll();

// Apenas excluídos
$excluidos = $pacienteModel->onlyDeleted()->findAll();
```

### Exclusão e Restauração
```php
// Excluir (soft delete)
$pacienteModel->delete($id);

// Restaurar
$pacienteModel->update($id, ['deleted_at' => null]);

// Excluir permanentemente
$pacienteModel->delete($id, true);
```

### Verificações
```php
// Verificar se existe (incluindo excluídos)
$existe = $pacienteModel->withDeleted()->find($id);

// Verificar se está excluído
$excluido = $pacienteModel->onlyDeleted()->find($id);
```

## Models com Soft Delete

- ✅ AtendimentoModel
- ✅ AtendimentoExameModel  
- ✅ AtendimentoProcedimentoModel
- ✅ BairroModel
- ✅ ExameModel
- ✅ LogradouroModel
- ✅ MedicoModel
- ✅ PacienteModel
- ✅ ProcedimentoModel

## Estrutura de Tabelas

Todas as tabelas possuem:
```sql
created_at DATETIME NULL
updated_at DATETIME NULL  
deleted_at DATETIME NULL
```

## Configuração do Model

```php
protected $useSoftDeletes = true;
protected $deletedField = 'deleted_at';
protected $useTimestamps = true;
```

## Troubleshooting

### Registro não aparece
```php
// Verificar se foi excluído
$model->withDeleted()->find($id);
```

### Performance lenta
```bash
# Limpar registros antigos
php spark softdelete:manage cleanup --days=30
```

### Erro de helper
```php
// Carregar helper manualmente
helper('softdelete');
```
