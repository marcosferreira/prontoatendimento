# Sistema de Pronto Atendimento

Sistema de gestão para Pronto Atendimento desenvolvido em CodeIgniter 4, com foco na segurança e integridade dos dados através de soft delete.

## Características Principais

- ✅ **Soft Delete** habilitado em todos os models
- ✅ Sistema de auditoria e recuperação de dados
- ✅ Interface web responsiva
- ✅ Gestão completa de pacientes, médicos e atendimentos
- ✅ Controle de procedimentos e exames
- ✅ Sistema de localização (bairros e logradouros)

## Tecnologias

- **Framework:** CodeIgniter 4
- **Linguagem:** PHP 8.1+
- **Banco de Dados:** MySQL/MariaDB
- **Frontend:** Bootstrap, JavaScript
- **CLI:** Comandos personalizados para gestão

## Documentação

### Sistema de Soft Delete
- 📋 [Documentação Completa](docs/soft-delete-system.md) - Visão geral e guia do usuário
- 🔧 [Documentação Técnica](docs/soft-delete-technical-documentation.md) - Detalhes para desenvolvedores
- ⚡ [Referência Rápida](docs/soft-delete-quick-reference.md) - Comandos e código essenciais

### Outras Documentações
- 📁 [Análise do Sistema](docs/analysis/)
- 🗄️ [Estrutura do Banco](docs/database/)
- 📋 [Requisitos](docs/requeriments/)

## Instalação

1. **Clone o repositório:**
   ```bash
   git clone [repository-url]
   cd prontoatendimento
   ```

2. **Instale as dependências:**
   ```bash
   composer install
   ```

3. **Configure o ambiente:**
   ```bash
   cp env .env
   # Edite .env com suas configurações de banco
   ```

4. **Execute as migrações:**
   ```bash
   php spark migrate
   ```

5. **Popule o banco (opcional):**
   ```bash
   php spark db:seed BairroSeeder
   ```

## Comandos CLI Disponíveis

### Gestão de Soft Delete
```bash
# Ver estatísticas de registros excluídos
php spark softdelete:manage stats

# Limpar registros antigos (30 dias por padrão)
php spark softdelete:manage cleanup

# Limpar registros de 60 dias sem confirmação
php spark softdelete:manage cleanup --days=60 --force

# Restaurar um registro específico
php spark softdelete:manage restore --model=PacienteModel --id=123
```

### Outros Comandos
```bash
# Verificar usuários online
php spark check:lastactive

# Criar super admin
php spark create:superadmin

# Gerenciar soft deletes
php spark softdelete:manager
```

## Estrutura do Projeto

```
app/
├── Commands/          # Comandos CLI personalizados
├── Controllers/       # Controladores da aplicação
├── Models/           # Models com soft delete habilitado
├── Views/            # Templates e views
├── Database/
│   ├── Migrations/   # Migrações do banco
│   └── Seeds/        # Seeders para popular dados
└── Config/           # Configurações da aplicação

docs/                 # Documentação completa
├── soft-delete-system.md
├── soft-delete-technical-documentation.md
└── soft-delete-quick-reference.md
```

## Models com Soft Delete

Todos os models principais possuem soft delete habilitado:

- ✅ **AtendimentoModel** - Atendimentos médicos
- ✅ **PacienteModel** - Dados dos pacientes  
- ✅ **MedicoModel** - Cadastro de médicos
- ✅ **ExameModel** - Tipos de exames
- ✅ **ProcedimentoModel** - Procedimentos médicos
- ✅ **BairroModel** - Bairros da cidade
- ✅ **LogradouroModel** - Logradouros/endereços
- ✅ **AtendimentoExameModel** - Relação atendimento-exame
- ✅ **AtendimentoProcedimentoModel** - Relação atendimento-procedimento

## Requisitos do Sistema

**PHP:** versão 8.1 ou superior

**Extensões obrigatórias:**
- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)
- [json](http://php.net/manual/en/json.installation.php) (habilitado por padrão)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) (para MySQL)
- [libcurl](http://php.net/manual/en/curl.requirements.php) (para requisições HTTP)

**Banco de Dados:**
- MySQL 5.7+ ou MariaDB 10.3+

## Segurança e Backup

⚠️ **IMPORTANTE:** Este sistema utiliza soft delete para preservar dados críticos de saúde.

### Recomendações:
1. **Backups regulares** do banco de dados
2. **Limpeza periódica** de registros antigos via CLI
3. **Monitoramento** do crescimento das tabelas
4. **Auditoria** regular das exclusões

### Comandos de Manutenção:
```bash
# Verificar status dos dados
php spark softdelete:manage stats

# Limpeza mensal automatizada (via cron)
0 2 1 * * cd /path/to/project && php spark softdelete:manage cleanup --days=90 --force
```

## Contribuição

Para contribuir com o projeto:

1. Fork o repositório
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## Licença

Este projeto está licenciado sob a licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## Suporte

Para suporte e dúvidas:
- 📋 Consulte a [documentação completa](docs/)
- 🐛 Reporte bugs através das issues
- 💬 Discussões e dúvidas no fórum do projeto

---

> **Nota:** Este sistema foi desenvolvido especificamente para Pronto Atendimento, com foco em segurança, auditoria e recuperação de dados através do sistema de soft delete implementado.
