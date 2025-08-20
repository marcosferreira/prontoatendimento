# Funcionalidade de Cidade Externa para Pacientes

## Descrição

Esta funcionalidade permite que pacientes sejam cadastrados com endereços de outras cidades, além da cidade local do município. O sistema agora suporta dois tipos de endereço:

1. **Endereço Local**: Utiliza o sistema de bairros e logradouros cadastrados no município
2. **Endereço Externo**: Permite cadastrar pacientes de outras cidades com endereço livre

## Implementação

### 1. Migration - Campos Adicionados

**Arquivo**: `app/Database/Migrations/2025-08-20-102853_AddCidadeToPacientes.php`

Novos campos na tabela `pam_pacientes`:
- `cidade_externa` (VARCHAR 100) - Nome da cidade quando o paciente não reside na cidade local
- `logradouro_externo` (VARCHAR 255) - Endereço completo quando o paciente reside em outra cidade  
- `cep_externo` (VARCHAR 10) - CEP quando o paciente reside em outra cidade

### 2. Modelo PacienteModel

**Atualizações**:
- Adicionados novos campos em `$allowedFields`
- Adicionadas validações para os novos campos
- Criados métodos auxiliares:
  - `isEnderecoExterno($paciente)` - Verifica se o paciente reside em outra cidade
  - `getEnderecoCompleto($paciente)` - Retorna o endereço completo formatado
  - `getCidadePaciente($paciente)` - Retorna a cidade do paciente (local ou externa)

### 3. Controller Pacientes

**Atualizações**:
- Adicionados novos campos nas validações dos métodos `store()` e `update()`
- Incluídos novos campos no processamento de dados

### 4. Views

#### 4.1 Index (Listagem)
- Modificada a tabela para mostrar "Endereço" e "Cidade" ao invés de "Logradouro" e "Bairro"
- Adicionada lógica para exibir endereço completo baseado no tipo
- Cidades externas são destacadas com badge azul

#### 4.2 Modal de Cadastro
- Adicionados radio buttons para escolher tipo de endereço
- Campos condicionais que se alternam baseado na seleção:
  - **Local**: Bairro + Logradouro (campos relacionais)
  - **Externo**: Cidade + Endereço Completo + CEP (campos livres)
- JavaScript para controlar exibição e validação dos campos

#### 4.3 Formulário de Edição
- Mesma estrutura do modal de cadastro
- Campos pré-preenchidos baseado no tipo de endereço atual
- JavaScript para alternar entre tipos preservando dados já cadastrados

#### 4.4 Formulário de Criação (create.php)
- Interface idêntica ao modal de cadastro
- Controle de validação condicional
- Preserva dados em caso de erro de validação
- JavaScript para alternância entre tipos de endereço

#### 4.5 Modal de Visualização (modal_view.php)
- Exibe endereço completo baseado no tipo
- Badge diferenciado para cidades externas
- Separação clara entre endereço e cidade
- Informações de CEP apropriadas para cada tipo

#### 4.6 Página de Detalhes (show.php)
- Exibição completa e detalhada do endereço
- Badge "Externa" para cidades não locais
- Formatação clara de endereço, cidade e CEP
- Layout otimizado para leitura

#### 4.7 Relatório de Impressão (print.php)
- Formatação adequada para impressão
- Endereço completo em linha única
- Identificação clara de cidades externas
- Layout otimizado para papel

## Lógica de Negócio

### Validações
- Quando "Endereço Local" é selecionado:
  - Campos de cidade externa são limpos e tornam-se opcionais
  - Campos de bairro/logradouro ficam disponíveis
  
- Quando "Outra Cidade" é selecionado:
  - Campo "Cidade" torna-se obrigatório
  - Campos de bairro/logradouro são limpos e ficam ocultos
  - Endereço e CEP externos ficam disponíveis

### Exibição de Dados
- Na listagem, a coluna "Cidade" mostra:
  - Nome do bairro (para endereços locais)
  - Nome da cidade com badge azul (para endereços externos)

- Na listagem, a coluna "Endereço" mostra:
  - Tipo + Nome do logradouro, número (para endereços locais)
  - Endereço completo informado (para endereços externos)

## Compatibilidade

- **Totalmente compatível** com pacientes já cadastrados
- Pacientes existentes continuam funcionando normalmente
- Campos novos são opcionais (NULL permitido)
- Sistema detecta automaticamente o tipo baseado na presença de `cidade_externa`

## Benefícios

1. **Flexibilidade**: Permite atender pacientes de outras cidades
2. **Manutenção**: Não quebra funcionalidade existente
3. **Usabilidade**: Interface intuitiva com campos condicionais
4. **Dados estruturados**: Mantém relacionamentos para endereços locais
5. **Dados livres**: Permite flexibilidade para endereços externos

## Exemplos de Uso

### Paciente Local
```
Tipo: Endereço Local
Bairro: Centro
Logradouro: Rua das Flores
Número: 123
Complemento: Apto 4B
```

### Paciente de Outra Cidade
```
Tipo: Outra Cidade
Cidade: São Paulo
Endereço: Av. Paulista, 1000, Bela Vista
CEP: 01310-100
Número: 1000
Complemento: Conjunto 45
```

## Arquivos Modificados

1. `app/Database/Migrations/2025-08-20-102853_AddCidadeToPacientes.php` (novo)
2. `app/Models/PacienteModel.php` (atualizado)
3. `app/Controllers/Pacientes.php` (atualizado)
4. `app/Views/pacientes/index.php` (atualizado)
5. `app/Views/pacientes/edit.php` (atualizado)
6. `app/Views/pacientes/create.php` (atualizado)
7. `app/Views/pacientes/modal_view.php` (atualizado)
8. `app/Views/pacientes/print.php` (atualizado)
9. `app/Views/pacientes/show.php` (atualizado)
10. `docs/paciente-cidade-feature.md` (novo - este arquivo)

## Comandos Executados

```bash
# Criação da migration
php spark make:migration AddCidadeToPacientes

# Execução da migration
php spark migrate
```

## Considerações Técnicas

- Utiliza soft delete (campos deletedAt respeitados)
- Máscaras JavaScript aplicadas nos campos apropriados
- Validação client-side e server-side
- Queries otimizadas com JOINs apropriados
- Prefixo de tabela `pam_` aplicado automaticamente pelo CodeIgniter
