# Instruções para GitHub Copilot - Sistema de Pronto Atendimento Municipal (SisPAM)

## Contexto do Projeto

Este é um sistema de gestão de pronto atendimento municipal desenvolvido em PHP 8.1+ com framework CodeIgniter 4.x+. O sistema gerencia pacientes, médicos, atendimentos, exames, procedimentos e funcionalidades administrativas.

## Estrutura do Banco de Dados

### Prefixo das Tabelas
**IMPORTANTE**: Todas as tabelas do banco de dados usam o prefixo `pam_` conforme configurado no arquivo `.env`:
```properties
database.default.DBPrefix = pam_
```

### Tabelas Principais
- `pam_pacientes` - Dados dos pacientes
- `pam_medicos` - Cadastro de médicos
- `pam_atendimentos` - Registros de atendimentos
- `pam_exames` - Catálogo de exames
- `pam_procedimentos` - Catálogo de procedimentos
- `pam_atendimento_exames` - Relação N:N entre atendimentos e exames
- `pam_atendimento_procedimentos` - Relação N:N entre atendimentos e procedimentos
- `pam_bairros` - Cadastro de bairros
- `pam_logradouros` - Cadastro de logradouros/endereços
- `pam_configuracoes` - Configurações do sistema
- `pam_auditoria` - Logs de auditoria
- `pam_notificacoes` - Sistema de notificações

### Tabelas de Autenticação (CodeIgniter Shield)
- `pam_users` - Usuários do sistema
- `pam_auth_identities` - Identidades de autenticação
- `pam_auth_logins` - Tentativas de login
- `pam_auth_token_logins` - Logins via token
- `pam_auth_remember_tokens` - Tokens de "lembrar-me"
- `pam_auth_groups_users` - Relação usuários-grupos
- `pam_auth_permissions_users` - Permissões específicas de usuários

## Diretrizes de Desenvolvimento

### 1. Models
- Todos os models estendem `CodeIgniter\Model`
- Usar `useSoftDeletes = true` para exclusão lógica
- Sempre definir `primaryKey`, `table`, `allowedFields`
- Implementar validações apropriadas no model
- Todos os metodos devem ser colocado após os atributos e construtor
- Exemplo de estrutura:

```php
<?php
namespace App\Models;

use CodeIgniter\Model;

class ExemploModel extends Model
{
    protected $table = 'exemplos'; // SEM o prefixo pam_
    protected $primaryKey = 'id_exemplo';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = ['campo1', 'campo2'];
    
    // Timestamps automáticos
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    // Validações
    protected $validationRules = [
        'campo1' => 'required|max_length[255]',
    ];
}
```

### 2. Controllers
- Usar autenticação do CodeIgniter Shield
- Implementar logs de auditoria para ações importantes
- Validar dados antes de processar
- Retornar respostas JSON para AJAX
- Exemplo:

```php
<?php
namespace App\Controllers;

class ExemploController extends BaseController
{
    protected $exemploModel;
    
    public function __construct()
    {
        $this->exemploModel = new \App\Models\ExemploModel();
    }
    
    public function create()
    {
        if (!$this->exemploModel->save($data)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->exemploModel->errors()
            ]);
        }
        
        // Log de auditoria
        $this->logAuditoria('CREATE', 'Exemplo', $data);
        
        return $this->response->setJSON(['success' => true]);
    }
}
```

### 3. Queries e Relacionamentos
- O CodeIgniter 4 aplica automaticamente o prefixo `pam_` nas queries
- Para JOINs, usar apenas o nome da tabela sem prefixo:

```php
// CORRETO
$query = $this->select('pacientes.*, logradouros.nome_logradouro')
             ->join('logradouros', 'logradouros.id_logradouro = pacientes.id_logradouro', 'left')
             ->findAll();

// ERRADO - não usar prefixo manualmente
$query = $this->select('pam_pacientes.*, pam_logradouros.nome_logradouro')
             ->join('pam_logradouros', 'pam_logradouros.id_logradouro = pam_pacientes.id_logradouro', 'left')
             ->findAll();
```

### 4. Migrations
- Usar nomes descritivos com timestamp
- Definir Foreign Keys apropriadas
- Incluir campos created_at, updated_at, deleted_at
- Exemplo:

```php
public function up()
{
    $this->forge->addField([
        'id_exemplo' => [
            'type' => 'int',
            'constraint' => 11,
            'unsigned' => true,
            'auto_increment' => true,
        ],
        'nome' => [
            'type' => 'varchar',
            'constraint' => 255,
            'null' => false,
        ],
        'created_at' => ['type' => 'datetime', 'null' => true],
        'updated_at' => ['type' => 'datetime', 'null' => true],
        'deleted_at' => ['type' => 'datetime', 'null' => true],
    ]);
    
    $this->forge->addKey('id_exemplo', true);
    $this->forge->createTable('exemplos');
}
```

### 5. Padrões de Nomenclatura
- **Tabelas**: plural, sem prefixo no código (pacientes, medicos, atendimentos)
- **Primary Keys**: id_{nome_tabela_singular} (id_paciente, id_medico)
- **Foreign Keys**: mesmo nome da PK referenciada
- **Models**: singular, sufixo Model (PacienteModel, MedicoModel)
- **Controllers**: plural (Pacientes, Medicos, Atendimentos)

### 6. Relacionamentos Importantes
- Paciente → Logradouro (N:1)
- Logradouro → Bairro (N:1)
- Atendimento → Paciente (N:1)
- Atendimento → Medico (N:1)
- Atendimento ↔ Exames (N:N)
- Atendimento ↔ Procedimentos (N:N)

### 7. Campos Especiais
- **Classificação de Risco**: enum('Verde', 'Amarelo', 'Vermelho', 'Azul')
- **Status Médico**: enum('Ativo', 'Inativo')
- **Tipo Logradouro**: enum com valores pré-definidos
- **Soft Delete**: usar `deleted_at` em todas as tabelas principais

### 8. Sistema de Auditoria
- Implementar logs para CREATE, UPDATE, DELETE
- Usar AuditoriaModel para registrar ações
- Capturar dados anteriores e novos
- Incluir informações do usuário e IP

### 9. Validações Comuns
```php
// CPF único
'cpf' => 'required|is_unique[pacientes.cpf,id_paciente,{id}]'

// Email opcional mas válido
'email' => 'permit_empty|valid_email'

// Referência a outra tabela
'id_bairro' => 'required|is_natural_no_zero'

// Campos opcionais com limite
'observacoes' => 'permit_empty|max_length[1000]'
```

### 10. Configuração do Ambiente
- **Base URL**: http://localhost:8080/
- **Banco**: MySQL/MariaDB
- **Host**: 127.0.0.1:3306
- **Database**: prontoatendimento_db
- **Usuário**: prontoatendimento
- **Senha**: prontoatendimento

### 11. Recursos Específicos do Sistema
- **Soft Delete**: Habilitado em todas as tabelas principais
- **Timestamps**: Automáticos (created_at, updated_at)
- **Auditoria**: Logs automáticos de ações importantes
- **Notificações**: Sistema de alertas e notificações
- **Configurações**: Sistema centralizados de configurações
- **Backup**: Sistema automatizado de backup

### 12. Boas Práticas de Segurança
- Usar prepared statements (automático no CodeIgniter)
- Validar todas as entradas
- Escapar dados de saída
- Implementar autenticação em todos os controllers
- Usar CSRF protection
- Logs de auditoria para rastreabilidade

### 13. Frontend
- Bootstrap 5 para UI
- jQuery para interações
- DataTables para listagens
- SweetAlert2 para alertas
- Axios para requisições AJAX

### 14. Estrutura de Arquivos Importante
```
app/
├── Controllers/       # Controladores
├── Models/           # Modelos de dados
├── Views/            # Templates/Views
├── Database/
│   └── Migrations/   # Migrações do banco
├── Config/           # Configurações
└── Helpers/          # Funções auxiliares

docs/                 # Documentação técnica
public/assets/        # CSS, JS, imagens
writable/logs/        # Logs do sistema
```

## Comandos Úteis

```bash
# Executar migrações
php spark migrate

# Reverter migração
php spark migrate:rollback

# Criar nova migração
php spark make:migration NomeDaMigracao

# Criar model
php spark make:model NomeModel

# Criar controller
php spark make:controller NomeController

# Limpar cache
php spark cache:clear
```

## Lembre-se Sempre

1. **NUNCA** usar o prefixo `pam_` manualmente nas queries
2. **SEMPRE** usar soft deletes nas tabelas principais
3. **SEMPRE** implementar validações nos models
4. **SEMPRE** usar logs de auditoria para ações importantes
5. **SEMPRE** validar dados de entrada nos controllers
6. **SEMPRE** usar o método .countAllResults() para manter a quantidade baseada no soft delete
7. **SEMPRE** seguir os padrões de nomenclatura estabelecidos
8. **SEMPRE** considerar relacionamentos existentes ao modificar estruturas
9. **SEMPRE** manter a documentação atualizada com novas funcionalidades e alterações
10. **SEMPRE** crie um plano de implementação antes as alterações significativas
11. **SEMPRE** implemente javascript vanilla sempre que possível, evitando bibliotecas desnecessárias, nunca utilize JQuery
# Instruções para GitHub Copilot - Sistema de Pronto Atendimento Municipal (SisPAM)

## Contexto do Projeto

Este é um sistema de gestão de pronto atendimento municipal desenvolvido em PHP 8.1+ com framework CodeIgniter 4.x+. O sistema gerencia pacientes, médicos, atendimentos, exames, procedimentos e funcionalidades administrativas.

## Estrutura do Banco de Dados

### Prefixo das Tabelas
**IMPORTANTE**: Todas as tabelas do banco de dados usam o prefixo `pam_` conforme configurado no arquivo `.env`:
```properties
database.default.DBPrefix = pam_
```

### Tabelas Principais
- `pam_pacientes` - Dados dos pacientes (inclui endereços externos)
- `pam_medicos` - Cadastro de médicos (vinculados a usuários do sistema)
- `pam_atendimentos` - Registros de atendimentos (suporte a observação clínica)
- `pam_exames` - Catálogo de exames (tipos: laboratorial, imagem, funcional, outros)
- `pam_procedimentos` - Catálogo de procedimentos
- `pam_atendimento_exames` - Relação N:N entre atendimentos e exames (com resultados e status)
- `pam_atendimento_procedimentos` - Relação N:N entre atendimentos e procedimentos (com quantidade)
- `pam_bairros` - Cadastro de bairros (com área de cobertura)
- `pam_logradouros` - Cadastro de logradouros/endereços (tipos completos)
- `pam_configuracoes` - Configurações centralizadas do sistema (com categorias e tipos)
- `pam_auditoria` - Logs de auditoria (sem soft delete)
- `pam_notificacoes` - Sistema de notificações inteligentes (com severidade e conjunto de hash)
- `pam_notificacao_atendimentos` - Relação N:N entre notificações e atendimentos
- `pam_backups` - Histórico e controle de backups do sistema

### Tabelas de Autenticação (CodeIgniter Shield)
- `pam_users` - Usuários do sistema
- `pam_auth_identities` - Identidades de autenticação
- `pam_auth_logins` - Tentativas de login
- `pam_auth_token_logins` - Logins via token
- `pam_auth_remember_tokens` - Tokens de "lembrar-me"
- `pam_auth_groups_users` - Relação usuários-grupos
- `pam_auth_permissions_users` - Permissões específicas de usuários

## Diretrizes de Desenvolvimento

### 1. Models
- Todos os models estendem `CodeIgniter\Model`
- Usar `useSoftDeletes = true` para exclusão lógica
- Sempre definir `primaryKey`, `table`, `allowedFields`
- Implementar validações apropriadas no model
- Todos os metodos devem ser colocado após os atributos e construtor
- Exemplo de estrutura:

```php
<?php
namespace App\Models;

use CodeIgniter\Model;

class ExemploModel extends Model
{
    protected $table = 'exemplos'; // SEM o prefixo pam_
    protected $primaryKey = 'id_exemplo';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = ['campo1', 'campo2'];
    
    // Timestamps automáticos
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    // Validações
    protected $validationRules = [
        'campo1' => 'required|max_length[255]',
    ];
}
```

### 2. Controllers
- Usar autenticação do CodeIgniter Shield
- Implementar logs de auditoria para ações importantes
- Validar dados antes de processar
- Retornar respostas JSON para AJAX
- Exemplo:

```php
<?php
namespace App\Controllers;

class ExemploController extends BaseController
{
    protected $exemploModel;
    
    public function __construct()
    {
        $this->exemploModel = new \App\Models\ExemploModel();
    }
    
    public function create()
    {
        if (!$this->exemploModel->save($data)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->exemploModel->errors()
            ]);
        }
        
        // Log de auditoria
        $this->logAuditoria('CREATE', 'Exemplo', $data);
        
        return $this->response->setJSON(['success' => true]);
    }
}
```

### 3. Queries e Relacionamentos
- O CodeIgniter 4 aplica automaticamente o prefixo `pam_` nas queries
- Para JOINs, usar apenas o nome da tabela sem prefixo:

```php
// CORRETO
$query = $this->select('pacientes.*, logradouros.nome_logradouro')
             ->join('logradouros', 'logradouros.id_logradouro = pacientes.id_logradouro', 'left')
             ->findAll();

// ERRADO - não usar prefixo manualmente
$query = $this->select('pam_pacientes.*, pam_logradouros.nome_logradouro')
             ->join('pam_logradouros', 'pam_logradouros.id_logradouro = pam_pacientes.id_logradouro', 'left')
             ->findAll();
```

### 4. Migrations
- Usar nomes descritivos com timestamp
- Definir Foreign Keys apropriadas
- Incluir campos created_at, updated_at, deleted_at
- Exemplo:

```php
public function up()
{
    $this->forge->addField([
        'id_exemplo' => [
            'type' => 'int',
            'constraint' => 11,
            'unsigned' => true,
            'auto_increment' => true,
        ],
        'nome' => [
            'type' => 'varchar',
            'constraint' => 255,
            'null' => false,
        ],
        'created_at' => ['type' => 'datetime', 'null' => true],
        'updated_at' => ['type' => 'datetime', 'null' => true],
        'deleted_at' => ['type' => 'datetime', 'null' => true],
    ]);
    
    $this->forge->addKey('id_exemplo', true);
    $this->forge->createTable('exemplos');
}
```

### 5. Padrões de Nomenclatura
- **Tabelas**: plural, sem prefixo no código (pacientes, medicos, atendimentos)
- **Primary Keys**: id_{nome_tabela_singular} (id_paciente, id_medico)
- **Foreign Keys**: mesmo nome da PK referenciada
- **Models**: singular, sufixo Model (PacienteModel, MedicoModel)
- **Controllers**: plural (Pacientes, Medicos, Atendimentos)

### 6. Relacionamentos Importantes
- Paciente → Logradouro (N:1) - `id_logradouro` opcional para endereços externos
- Logradouro → Bairro (N:1) - `id_bairro` obrigatório  
- Atendimento → Paciente (N:1) - `id_paciente` obrigatório
- Atendimento → Medico (N:1) - `id_medico` obrigatório
- Atendimento ↔ Exames (N:N) - através da tabela `atendimento_exames`
- Atendimento ↔ Procedimentos (N:N) - através da tabela `atendimento_procedimentos`
- Medico → User (1:1) - `id_user` opcional para vinculação de acesso
- Notificacao ↔ Atendimentos (N:N) - através da tabela `notificacao_atendimentos`
- AtendimentoExame → Exame (N:1) - `id_exame` obrigatório
- AtendimentoExame → Atendimento (N:1) - `id_atendimento` obrigatório
- AtendimentoProcedimento → Procedimento (N:1) - `id_procedimento` obrigatório
- AtendimentoProcedimento → Atendimento (N:1) - `id_atendimento` obrigatório

### 7. Campos Especiais
- **Classificação de Risco**: enum('Vermelho','Laranja','Amarelo','Verde','Azul','Sem classificação') - Protocolo de Manchester
- **Status Médico**: enum('Ativo','Inativo')
- **Status Atendimento**: enum('Em Andamento','Finalizado','Cancelado','Aguardando','Suspenso')
- **Status Exame**: enum('Solicitado','Realizado','Cancelado')
- **Tipo Exame**: enum('laboratorial','imagem','funcional','outros')
- **Tipo Logradouro**: enum('Rua','Avenida','Travessa','Alameda','Praça','Estrada','Sítio','Rodovia','Via','Beco','Largo')
- **Encaminhamento**: enum('Alta','Internação','Transferência','Especialista','Retorno','Óbito')
- **Severidade Notificação**: enum('baixa','media','alta','critica')
- **Status Notificação**: enum('ativa','resolvida','cancelada')
- **Tipo Notificação**: enum('paciente_recorrente','surto_sintomas','alta_demanda','medicamento_critico','equipamento_falha','estatistica_anomala')
- **Paciente Observação**: enum('Sim','Não') - para observação clínica
- **Sexo**: enum('M','F')
- **Soft Delete**: usar `deleted_at` em todas as tabelas principais (exceto `auditoria` e `backups`)

### 8. Sistema de Auditoria
- Implementar logs para CREATE, UPDATE, DELETE
- Usar AuditoriaModel para registrar ações
- Capturar dados anteriores e novos
- Incluir informações do usuário e IP

### 9. Validações Comuns
```php
// CPF único
'cpf' => 'required|is_unique[pacientes.cpf,id_paciente,{id}]'

// Email opcional mas válido
'email' => 'permit_empty|valid_email'

// Referência a outra tabela
'id_bairro' => 'required|is_natural_no_zero'

// Campos opcionais com limite
'observacoes' => 'permit_empty|max_length[1000]'
```

### 10. Configuração do Ambiente
- **Base URL**: http://localhost:8080/
- **Banco**: MySQL/MariaDB
- **Host**: 127.0.0.1:3306
- **Database**: prontoatendimento_db
- **Usuário**: prontoatendimento
- **Senha**: prontoatendimento

### 11. Recursos Específicos do Sistema
- **Soft Delete**: Habilitado em todas as tabelas principais
- **Timestamps**: Automáticos (created_at, updated_at)
- **Auditoria**: Logs automáticos de ações importantes
- **Notificações**: Sistema de alertas e notificações
- **Configurações**: Sistema centralizados de configurações
- **Backup**: Sistema automatizado de backup

### 12. Boas Práticas de Segurança
- Usar prepared statements (automático no CodeIgniter)
- Validar todas as entradas
- Escapar dados de saída
- Implementar autenticação em todos os controllers
- Usar CSRF protection
- Logs de auditoria para rastreabilidade

### 13. Frontend
- Bootstrap 5 para UI
- jQuery para interações
- DataTables para listagens
- SweetAlert2 para alertas
- Axios para requisições AJAX

### 14. Estrutura de Arquivos Importante
```
app/
├── Controllers/       # Controladores
├── Models/           # Modelos de dados
├── Views/            # Templates/Views
├── Database/
│   └── Migrations/   # Migrações do banco
├── Config/           # Configurações
└── Helpers/          # Funções auxiliares

docs/                 # Documentação técnica
public/assets/        # CSS, JS, imagens
writable/logs/        # Logs do sistema
```

## Comandos Úteis

```bash
# Executar migrações
php spark migrate

# Reverter migração
php spark migrate:rollback

# Criar nova migração
php spark make:migration NomeDaMigracao

# Criar model
php spark make:model NomeModel

# Criar controller
php spark make:controller NomeController

# Limpar cache
php spark cache:clear
```

## Lembre-se Sempre

1. **NUNCA** usar o prefixo `pam_` manualmente nas queries
2. **SEMPRE** usar soft deletes nas tabelas principais
3. **SEMPRE** implementar validações nos models
4. **SEMPRE** usar logs de auditoria para ações importantes
5. **SEMPRE** validar dados de entrada nos controllers
6. **SEMPRE** usar o método .countAllResults() para manter a quantidade baseada no soft delete
7. **SEMPRE** seguir os padrões de nomenclatura estabelecidos
8. **SEMPRE** considerar relacionamentos existentes ao modificar estruturas
9. **SEMPRE** manter a documentação atualizada com novas funcionalidades e alterações
10. **SEMPRE** crie um plano de implementação antes as alterações significativas
11. **SEMPRE** implemente javascript vanilla sempre que possível, evitando bibliotecas desnecessárias, nunca utilize JQuery
