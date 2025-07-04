# Índice da Documentação - Sistema de Pronto Atendimento

## 📋 Documentação Principal

### 🔒 Sistema de Soft Delete
- **[Documentação Completa](soft-delete-system.md)** - Visão geral, configuração e uso do sistema
- **[Documentação Técnica](soft-delete-technical-documentation.md)** - Detalhes de implementação para desenvolvedores
- **[Referência Rápida](soft-delete-quick-reference.md)** - Comandos e código essenciais

## 📁 Documentações Existentes

### 🏥 Sistema e Funcionalidades
- **[Administração](admin-area-documentation.md)** - Área administrativa do sistema
- **[Usuários Online](sistema-usuarios-online.md)** - Sistema de controle de usuários conectados
- **[Status de Usuários](campo-status-usuario.md)** - Gestão de status de usuários
- **[Filtros de Usuário](filtros-usuario-desativado.md)** - Sistema de filtros para usuários

### 🗺️ Localização e Endereços
- **[Logradouros](logradouros-feature-documentation.md)** - Feature de logradouros e endereços

### 🎨 Interface e Views
- **[Bairros](bairros-views-documentation.md)** - Sistema de gestão de bairros
- **[Logradouros](logradouros-feature-documentation.md)** - Feature de logradouros e endereços
- **[Atualização das Views de Pacientes](views/pacientes-views-update.md)** - Mudanças estruturais nas interfaces de pacientes

### 🔧 Técnicas e Configurações
- **[Models](models-documentation.md)** - Documentação dos models do sistema
- **[Email](email-fix-documentation.md)** - Configuração e correções de email
- **[Sidebar](sidebar-colapsavel.md)** - Interface responsiva da sidebar

## 📊 Análises e Estruturas

### 📈 Análises do Sistema
- **[analysis/](analysis/)** - Pasta com análises detalhadas do sistema

### 🗄️ Banco de Dados
- **[database/](database/)** - Documentação da estrutura do banco de dados
- **[Estrutura da Tabela Pacientes](database/pacientes-table-structure.md)** - Detalhes completos da tabela pacientes
- **[Consolidação de Migrations - Pacientes](database/migration-consolidation-pacientes.md)** - Histórico de consolidação
- **[Reestruturação Pacientes-Logradouros](database/table-restructure-pacientes-logradouros.md)** - Mudanças estruturais
- **[Sincronização da Entidade Logradouros](database/logradouros-entity-sync.md)** - Correções na entidade logradouros
- **[tables/](tables/)** - Documentação específica das tabelas
- **[tabless/](tabless/)** - Estruturas adicionais

### 📋 Requisitos
- **[requeriments/](requeriments/)** - Requisitos funcionais e não funcionais do sistema

## 🚀 Guias de Início Rápido

### Para Usuários
1. Leia a [Documentação do Soft Delete](soft-delete-system.md)
2. Consulte a [Referência Rápida](soft-delete-quick-reference.md) para comandos
3. Verifique a documentação específica da funcionalidade que precisa

### Para Desenvolvedores
1. Estude a [Documentação Técnica do Soft Delete](soft-delete-technical-documentation.md)
2. Consulte a [Documentação dos Models](models-documentation.md)
3. Revise as estruturas em [database/](database/) e [analysis/](analysis/)

### Para Administradores
1. Configure o sistema seguindo a [Documentação de Administração](admin-area-documentation.md)
2. Implemente monitoramento com base no [Sistema de Usuários Online](sistema-usuarios-online.md)
3. Configure manutenção automática usando os comandos de [Soft Delete](soft-delete-quick-reference.md)

## 🔍 Busca por Funcionalidade

| Funcionalidade | Documentação |
|----------------|--------------|
| **Exclusão Segura** | [Soft Delete System](soft-delete-system.md) |
| **Gestão de Usuários** | [Usuários Online](sistema-usuarios-online.md), [Status](campo-status-usuario.md) |
| **Localização** | [Bairros](bairros-views-documentation.md), [Logradouros](logradouros-feature-documentation.md) |
| **Interface** | [Sidebar](sidebar-colapsavel.md), [Admin](admin-area-documentation.md) |
| **Banco de Dados** | [Models](models-documentation.md), [Database](database/), [Pacientes](database/pacientes-table-structure.md) |
| **Email** | [Email Fix](email-fix-documentation.md) |
| **Filtros** | [Filtros de Usuário](filtros-usuario-desativado.md) |

## 📝 Como Contribuir com a Documentação

1. **Novas funcionalidades:** Crie documentação seguindo o padrão dos arquivos existentes
2. **Atualizações:** Mantenha as documentações atualizadas conforme mudanças no código
3. **Organização:** Use os diretórios apropriados (`analysis/`, `database/`, `requeriments/`)
4. **Índice:** Atualize este índice quando adicionar novos documentos

## 🏷️ Convenções de Nomenclatura

- **Funcionalidades:** `nome-funcionalidade-documentation.md`
- **Sistemas:** `sistema-nome-do-sistema.md`
- **Técnicas:** `nome-technical-documentation.md`
- **Referências:** `nome-quick-reference.md`
- **Análises:** `analysis/nome-da-analise.md`
- **Banco:** `database/nome-da-estrutura.md`

## 📧 Suporte

Para dúvidas sobre a documentação:
1. Verifique se existe documentação específica para sua dúvida
2. Consulte as análises em `analysis/` para entender o contexto
3. Revise os requisitos em `requeriments/` para compreender as regras de negócio

---

> **Dica:** Use Ctrl+F para buscar termos específicos neste índice e encontrar rapidamente a documentação que precisa!
