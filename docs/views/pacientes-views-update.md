# Atualização das Views de Pacientes

## Resumo

Este documento descreve as atualizações realizadas nas views de pacientes para refletir a nova estrutura de dados após a remoção dos campos redundantes de endereço e a implementação do relacionamento adequado com a tabela de logradouros.

## Data da Atualização
**04 de julho de 2025**

## Mudanças Estruturais

### Campos Removidos
- `endereco` - Campo de texto livre para endereço
- `cep` - CEP do paciente
- `cidade` - Cidade do paciente

### Nova Estrutura de Endereço
- `id_logradouro` - Referência para a tabela logradouros (obrigatório)
- `numero` - Número da residência (opcional)
- `complemento` - Complemento do endereço (opcional)

## Views Atualizadas

### 1. `index.php`
**Mudanças realizadas:**
- ✅ Atualizada exibição do preview de endereço na listagem para usar `nome_logradouro` + `numero`
- ✅ Reformulado formulário modal para usar seleção de bairro → logradouro
- ✅ Removidos campos CEP, endereço livre e cidade
- ✅ Adicionados campos obrigatórios para bairro e logradouro
- ✅ Implementado JavaScript para filtrar logradouros por bairro selecionado
- ✅ Removida funcionalidade de busca automática por CEP

### 2. `create.php`
**Mudanças realizadas:**
- ✅ Reformulada seção de endereço para usar estrutura de relacionamento
- ✅ Campos de bairro e logradouro marcados como obrigatórios
- ✅ Removidos campos CEP, endereço livre e cidade
- ✅ Mantida funcionalidade de filtro de logradouros por bairro
- ✅ Atualizado JavaScript removendo busca por CEP
- ✅ Preservada funcionalidade de seleção prévia de bairro/logradouro via URL

### 3. `edit.php`
**Mudanças realizadas:**
- ✅ Reformulada seção de endereço seguindo o mesmo padrão do create
- ✅ Implementada lógica para pré-selecionar bairro baseado no logradouro atual
- ✅ Removidos campos CEP, endereço livre e cidade
- ✅ Campos de bairro e logradouro marcados como obrigatórios
- ✅ Atualizado JavaScript removendo busca por CEP
- ✅ Mantida sincronização entre seleção de bairro e logradouros disponíveis

### 4. `show.php`
**Mudanças realizadas:**
- ✅ Consolidada exibição de endereço em um único campo formatado
- ✅ Implementada formatação completa: tipo + nome do logradouro, número, complemento, bairro, cidade e CEP
- ✅ Removidas seções separadas para cada campo de endereço

### 5. `modal_view.php`
**Mudanças realizadas:**
- ✅ Consolidada exibição de endereço em formato compacto
- ✅ Implementada formatação: tipo + nome do logradouro, número, complemento e bairro
- ✅ Removidas linhas separadas para logradouro e bairro

### 6. `print.php`
**Mudanças realizadas:**
- ✅ Consolidada exibição de endereço completo para impressão
- ✅ Formatação completa incluindo todos os componentes do endereço
- ✅ Otimizada para impressão em formato compacto

## Funcionalidades JavaScript

### Implementadas
1. **Filtro de Logradouros por Bairro**
   - Ao selecionar um bairro, apenas logradouros desse bairro são exibidos
   - Implementado em todas as views de formulário (index modal, create, edit)

2. **Pré-seleção de Bairro no Edit**
   - Identifica automaticamente o bairro do logradouro atual
   - Pré-seleciona o bairro correto ao carregar a página de edição

### Removidas
1. **Busca Automática por CEP (ViaCEP)**
   - Funcionalidade removida de todas as views
   - JavaScript de máscara de CEP removido

## Validações

### Campos Obrigatórios
- `id_bairro` - Bairro deve ser selecionado
- `id_logradouro` - Logradouro deve ser selecionado

### Campos Opcionais
- `numero` - Número da residência
- `complemento` - Informações adicionais do endereço

## Formatação de Exibição

### Padrão de Formatação
```
[Tipo] [Nome do Logradouro], [Número] - [Complemento]
Bairro: [Nome do Bairro]
Cidade: [Nome da Cidade]
CEP: [CEP]
```

### Exemplo
```
Rua das Flores, 123 - Apto 45
Bairro: Centro
Cidade: São Paulo
CEP: 01234-567
```

## Compatibilidade

### Dados Existentes
- Views são compatíveis com pacientes que já possuem `id_logradouro` preenchido
- Pacientes sem logradouro definido exibirão "Não informado"
- Dados de CEP e cidade são obtidos da tabela `logradouros` via relacionamento

### Controller
- As views dependem dos métodos atualizados no `PacienteModel`:
  - `getPacientesWithLogradouro()`
  - `getPacienteWithLogradouro($id)`
- Requer que o controller passe as variáveis `$bairros` e `$logradouros`

## Próximas Etapas

1. **Testes de Funcionalidade**
   - Testar cadastro de novos pacientes
   - Testar edição de pacientes existentes
   - Validar exibição de endereços em todas as views

2. **Validação de Dados**
   - Verificar se todos os logradouros têm bairros associados
   - Confirmar que pacientes existentes têm logradouros válidos

3. **Otimizações**
   - Considerar cache para lista de bairros/logradouros
   - Implementar busca dinâmica em caso de muitos logradouros

## Notas Técnicas

- Todas as views mantêm backward compatibility para campos já existentes
- JavaScript utiliza data attributes para relacionamento bairro-logradouro
- Formatação de endereço é responsiva e funciona em diferentes contextos (lista, visualização, impressão)
- Validação client-side mantida para campos obrigatórios
