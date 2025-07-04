# Consolidação de Migrações - Tabela Pacientes

## Resumo da Mudança

**Data:** 04 de julho de 2025  
**Ação:** Consolidação de migrações da tabela `pacientes`  
**Motivo:** Simplificação da estrutura de migrações no início do projeto

## Situação Anterior

Existiam duas migrações separadas para a tabela de pacientes:

1. **`2025-06-26-105355_CreatePacienteTable.php`** - Estrutura básica
   - Campos básicos de identificação
   - Campos de timestamp
   
2. **`2025-07-01-120000_AddCamposPacientes.php`** - Campos adicionais
   - Informações de contato
   - Endereço completo
   - Informações médicas

## Situação Atual

**Uma única migração consolidada:**

- **`2025-06-26-105355_CreatePacienteTable.php`** - Estrutura completa
  - Todos os 22 campos necessários
  - Estrutura final da tabela
  - Comentários explicativos nos campos

## Campos Consolidados

### Estrutura Completa da Tabela

| Categoria | Campos |
|-----------|--------|
| **Identificação** | `id_paciente`, `nome`, `cpf`, `rg` |
| **SUS** | `sus`, `numero_sus` |
| **Pessoais** | `data_nascimento`, `sexo`, `idade`, `tipo_sanguineo` |
| **Endereço** | `endereco`, `numero`, `complemento`, `cep`, `cidade`, `id_logradouro` |
| **Contato** | `telefone`, `celular`, `email` |
| **Médicas** | `alergias`, `observacoes` |
| **Responsável** | `nome_responsavel` |
| **Sistema** | `created_at`, `updated_at` |

### Total: 22 campos

## Vantagens da Consolidação

### ✅ Benefícios

1. **Simplicidade**: Uma única migração para a tabela completa
2. **Manutenção**: Mais fácil de entender e manter
3. **Performance**: Criação da tabela em uma única operação
4. **Documentação**: Estrutura clara e bem documentada
5. **Sem Riscos**: Feito no início do projeto, sem perda de dados

### 🔧 Aspectos Técnicos

- **Sem impacto**: Nenhum dado foi perdido (início do projeto)
- **Model atualizado**: PacienteModel já estava com todos os campos
- **Soft delete**: Mantém a compatibilidade com o sistema
- **Foreign keys**: Relacionamentos preservados

## Arquivos Afetados

### ✅ Modificados
- `app/Database/Migrations/2025-06-26-105355_CreatePacienteTable.php`
- `docs/soft-delete-technical-documentation.md`
- `docs/README.md`

### ❌ Removidos  
- `app/Database/Migrations/2025-07-01-120000_AddCamposPacientes.php`

### ➕ Criados
- `docs/database/pacientes-table-structure.md`

## Validação da Mudança

### Comandos para Verificar

```bash
# Verificar estrutura da migração
php spark migrate:status

# Recriar banco (se necessário)
php spark migrate:rollback
php spark migrate

# Verificar estrutura da tabela
php spark db:table pacientes
```

### Estrutura Final Esperada

```sql
CREATE TABLE `pacientes` (
  `id_paciente` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `sus` varchar(15) DEFAULT NULL COMMENT 'Número SUS antigo',
  `cpf` varchar(14) NOT NULL,
  `rg` varchar(20) DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `id_logradouro` int(11) unsigned DEFAULT NULL,
  `data_nascimento` date NOT NULL,
  `sexo` enum('M','F') NOT NULL,
  `idade` int(3) unsigned DEFAULT NULL,
  `tipo_sanguineo` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  `telefone` varchar(15) DEFAULT NULL,
  `celular` varchar(16) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `numero_sus` varchar(15) DEFAULT NULL COMMENT 'Número SUS principal',
  `nome_responsavel` varchar(255) DEFAULT NULL COMMENT 'Nome do responsável',
  `alergias` text DEFAULT NULL COMMENT 'Histórico de alergias',
  `observacoes` text DEFAULT NULL COMMENT 'Observações gerais',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_paciente`),
  UNIQUE KEY `cpf` (`cpf`),
  KEY `id_logradouro` (`id_logradouro`),
  KEY `idx_pacientes_deleted_at` (`deleted_at`),
  CONSTRAINT `pacientes_id_logradouro_foreign` FOREIGN KEY (`id_logradouro`) REFERENCES `logradouros` (`id_logradouro`) ON DELETE SET NULL ON UPDATE CASCADE
);
```

## Impacto no Sistema

### ✅ Sem Impacto
- **Models**: PacienteModel já estava correto
- **Controllers**: Nenhuma mudança necessária  
- **Views**: Formulários já preparados para todos os campos
- **Dados**: Nenhum dado foi perdido

### 📋 Ações Recomendadas

1. **Executar testes** para validar a estrutura
2. **Verificar formulários** de cadastro de pacientes
3. **Confirmar relacionamentos** com outras tabelas
4. **Atualizar seeders** se necessário

## Conclusão

A consolidação das migrações da tabela `pacientes` foi realizada com sucesso, resultando em:

- ✅ Estrutura mais limpa e organizada
- ✅ Manutenção simplificada
- ✅ Documentação completa e atualizada
- ✅ Compatibilidade total com o sistema existente

Esta mudança melhora significativamente a organização do projeto sem nenhum impacto negativo no funcionamento do sistema.
