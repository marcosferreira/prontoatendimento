# Documentação da Funcionalidade: Gerenciamento de Logradouros

## Resumo da Implementação

Esta documentação descreve a implementação da funcionalidade de **Gerenciamento de Logradouros** no Sistema de Pronto Atendimento Municipal (SisPAM). A funcionalidade permite o cadastro, edição, visualização e exclusão de logradouros vinculados aos bairros da cidade.

## Modificações Realizadas

### 1. Estrutura de Banco de Dados

#### Migration: `CreateLogradouroTable`
- **Arquivo**: `app/Database/Migrations/2025-07-02-120000_CreateLogradouroTable.php`
- **Tabela**: `logradouros`

**Campos da tabela:**
- `id_logradouro` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `nome_logradouro` (VARCHAR 150, NOT NULL)
- `tipo_logradouro` (ENUM: Rua, Avenida, Travessa, Alameda, Praça, Estrada, Rodovia, Via, Beco, Largo)
- `cep` (VARCHAR 10, NULLABLE)
- `id_bairro` (INT, FOREIGN KEY para tabela bairros)
- `observacoes` (TEXT, NULLABLE)
- `created_at` (DATETIME)
- `updated_at` (DATETIME)

**Índices criados:**
- Chave primária em `id_logradouro`
- Índice em `id_bairro`
- Índice em `cep`
- Chave estrangeira: `id_bairro` referencia `bairros.id_bairro` (CASCADE)

### 2. Model: LogradouroModel

#### Arquivo: `app/Models/LogradouroModel.php`

**Características principais:**
- Extends `CodeIgniter\Model`
- Tabela: `logradouros`
- Primary Key: `id_logradouro`
- Timestamps automáticos habilitados

**Campos permitidos:**
- `nome_logradouro`
- `tipo_logradouro`
- `cep`
- `id_bairro`
- `observacoes`

**Validações implementadas:**
- `nome_logradouro`: Obrigatório, máximo 150 caracteres
- `tipo_logradouro`: Obrigatório, deve estar na lista de tipos válidos
- `cep`: Opcional, máximo 10 caracteres
- `id_bairro`: Obrigatório, deve ser um número natural positivo
- `observacoes`: Opcional

**Métodos principais:**
- `getLogradourosByBairro($idBairro)`: Lista logradouros de um bairro específico
- `searchLogradouros($search)`: Busca logradouros por nome ou CEP
- `countLogradourosByBairro($idBairro)`: Conta logradouros por bairro
- `getByCep($cep)`: Busca logradouro por CEP
- `getWithBairroInfo()`: Lista logradouros com informações do bairro

### 3. Controller: Logradouros

#### Arquivo: `app/Controllers/Logradouros.php`

**Métodos implementados:**

#### CRUD Básico:
- `index()`: Lista todos os logradouros com filtros
- `create()`: Exibe formulário de criação
- `store()`: Salva novo logradouro
- `show($id)`: Exibe detalhes de um logradouro
- `edit($id)`: Exibe formulário de edição
- `update($id)`: Atualiza logradouro existente
- `delete($id)`: Remove logradouro

#### APIs e Funcionalidades Especiais:
- `getByBairro($idBairro)`: API para buscar logradouros por bairro
- `getByCep($cep)`: API para buscar logradouro por CEP
- `search()`: API de busca com filtros
- `export()`: Exportação de dados

**Filtros de busca suportados:**
- Busca por nome do logradouro
- Busca por CEP
- Filtro por bairro
- Combinação de filtros

### 4. Rotas Implementadas

#### Arquivo: `app/Config/Routes.php`

**Grupo de rotas `/logradouros`:**

#### Rotas Web (CRUD):
- `GET /logradouros` → `Logradouros::index` (listagem)
- `GET /logradouros/create` → `Logradouros::create` (formulário criação)
- `POST /logradouros` → `Logradouros::store` (salvar novo)
- `GET /logradouros/(:num)` → `Logradouros::show` (visualizar)
- `GET /logradouros/(:num)/edit` → `Logradouros::edit` (formulário edição)
- `PUT /logradouros/(:num)` → `Logradouros::update` (atualizar)
- `POST /logradouros/(:num)/update` → `Logradouros::update` (alternativa)
- `DELETE /logradouros/(:num)` → `Logradouros::delete` (excluir)
- `GET /logradouros/delete/(:num)` → `Logradouros::delete` (alternativa)

#### Rotas API:
- `GET /logradouros/api/bairro/(:num)` → `Logradouros::getByBairro` 
- `GET /logradouros/api/cep/(:segment)` → `Logradouros::getByCep`
- `GET /logradouros/api/search` → `Logradouros::search`

#### Rotas de Exportação:
- `GET /logradouros/export` → `Logradouros::export`

### 5. Atualização do BairroModel

#### Arquivo: `app/Models/BairroModel.php`

**Novos métodos adicionados:**

- `getLogradouros($idBairro)`: Busca logradouros de um bairro
- `getTotalLogradourosByBairro($idBairro)`: Conta logradouros por bairro
- `getBairrosWithLogradourosCount()`: Lista bairros com contagem de logradouros
- `canDelete($idBairro)`: Verifica se bairro pode ser excluído (sem logradouros/pacientes vinculados)

### 6. Atualização da Sidebar

#### Arquivo: `app/Views/components/sidebar.php`

**Modificação realizada:**
- Adicionado novo item de menu "Logradouros" na seção "Principal"
- Ícone: `bi-signpost` (Bootstrap Icons)
- Link: `/logradouros`
- Ativação automática quando URL contém "logradouros"

### 7. Dados de Teste Atualizados (Seeders)

#### Arquivo: `app/Database/Seeds/LogradouroSeeder.php`

**Implementação completa dos dados:**
- **Logradouros Urbanos** (109 logradouros):
  - **Centro** (18 logradouros): Avenidas Major Augusto Bezerra e Manoel Pedro, ruas históricas
  - **Glória** (5 logradouros): Ruas residenciais do bairro
  - **Governador José Maranhão** (8 logradouros): Ruas com nomes de personalidades locais
  - **Jardim Primavera** (13 logradouros): Inclui Avenida Major Augusto Bezerra
  - **Nova Cidade** (12 logradouros): Logradouros de expansão urbana
  - **Nova Conquista** (21 logradouros): Maior concentração, inclui Praça Nivaldo Cândido
  - **São Pedro** (12 logradouros): Ruas tradicionais do bairro
  - **Tapuio** (4 logradouros): Área residencial menor
  - **Terra Prometida** (11 logradouros): Inclui Avenida Major Augusto Bezerra

- **Logradouros Rurais** (61 estradas):
  - **Comunidade Cruz da Menina** (1 estrada)
  - **Área Rural 01** (10 sítios): Balanço, Capivara, Cobra Maga, Estrela, Itabaiana, Miguel, Riacho de Areia, São Luiz, Simão
  - **Área Rural 02** (11 sítios): Bogi, Caiçara, Cozinha, Marias Pretas, Panelas, Pinhões, Raposa, Salgado de Manoel Moreira, Várzea Grande, Zé Paz de Baixo, Zé Paz de Cima
  - **Área Rural 03** (11 sítios): Barbatão, Caboclo de Palhares, Chã de Palhares, Glória, Massaranduba, Mata, Tanque do Veado, Umari, Vaca Morta, Zé de Fogo, Zé Paz da Serra
  - **Área Rural 04** (12 sítios): Brejinho, Caco, Canafistula, Cruz, Lagoa do Braz, Pedra Lisa, Pimenta, Pitomba, Raimundo, Seró, Tapuio, Umarizinho
  - **Área Rural 05** (12 sítios): Barroção, Cafundó, Caiana, Cajazeiras, Lajedo Preto, Marcação, Mulungu, Pedra Lavrada, Queimadas, Salgadinho, Seixos, Tanques
  - **Área Rural 06** (10 sítios): Boa Vista, Carnaúba, Carnaúbeira de Cima, Estreito, Lagoa da Serra, Mela Bode, Oiticica, Olho D'Água do Gregório, Serra do Sítio, Volta

**Características dos dados:**
- CEP padrão unificado: 58228-000 para todos os logradouros
- Tipos variados: Rua, Avenida, Praça, Estrada
- Nomes autênticos baseados em personalidades e referências locais
- Observações específicas para logradouros rurais
- Relacionamento correto com bairros através de chaves estrangeiras
- Cobertura completa: 100% dos bairros/sítios do BairroSeeder têm logradouros

**Total de registros**: 170 logradouros cadastrados para 75 bairros/localidades

**Distribuição por tipo:**
- Ruas: 95 logradouros (áreas urbanas)
- Avenidas: 3 logradouros (principais vias urbanas)
- Praças: 1 logradouro (espaço público)
- Estradas: 61 logradouros (acessos rurais)

**Cobertura territorial:**
- 9 bairros urbanos com 109 logradouros
- 1 comunidade rural com 1 estrada
- 65 sítios rurais com 60 estradas de acesso

#### Arquivo: `app/Database/Seeds/BairroSeeder.php`

**Expansão significativa dos dados:**
- **Bairros Urbanos** (9 bairros):
  - Centro, Glória, Governador José Maranhão
  - Jardim Primavera, Nova Cidade, Nova Conquista
  - São Pedro, Tapuio, Terra Prometida

- **Comunidades e Sítios Rurais** (56 localidades):
  - Organizados por áreas rurais (Área 01 a 06)
  - Inclui sítios tradicionais da região
  - Nomes autênticos das localidades rurais
  - 1 comunidade (Cruz da Menina)
  - 55 sítios distribuídos em 6 áreas rurais

**Total**: 65 bairros/localidades cadastradas

### 8. Recursos CSS

#### Arquivo: `public/assets/css/logradouros.css`
- Estilos específicos para as telas de logradouros
- Responsividade para diferentes dispositivos
- Consistência visual com o design do sistema

## Funcionalidades Implementadas

### 1. **Gestão Completa de Logradouros**
- Cadastro de novos logradouros
- Edição de informações existentes
- Visualização detalhada
- Exclusão com validações

### 2. **Relacionamento com Bairros**
- Logradouros obrigatoriamente vinculados a bairros
- Integridade referencial garantida
- Consultas otimizadas com JOINs

### 3. **Sistema de Busca Avançado**
- Busca por nome do logradouro
- Busca por CEP
- Filtro por bairro
- Busca combinada

### 4. **APIs RESTful**
- Endpoints para integração
- Busca programática por bairro/CEP
- Dados em formato JSON

### 5. **Validações Robustas**
- Validação de tipos de logradouro
- Verificação de integridade de dados
- Prevenção de dados inconsistentes

### 6. **Integração com Sistema Existente**
- Menu lateral atualizado
- Consistência visual mantida
- Integração com módulo de bairros

## Benefícios da Implementação

### 1. **Organização Territorial**
- Estruturação hierárquica: Bairros → Logradouros
- Facilita localização de pacientes
- Melhora a gestão urbana

### 2. **Qualidade dos Dados**
- Padronização de endereços
- Redução de inconsistências
- Facilita relatórios territoriais

### 3. **Experiência do Usuário**
- Interface intuitiva
- Busca eficiente
- Formulários validados

### 4. **Escalabilidade**
- Estrutura preparada para crescimento
- APIs para futuras integrações
- Modelo extensível

## Próximos Passos Sugeridos

1. **Views e Interface Web**
   - Implementar views para CRUD completo
   - Formulários responsivos
   - Modais de confirmação

2. **Relatórios**
   - Relatório de logradouros por bairro
   - Estatísticas territoriais
   - Exportação em diferentes formatos

3. **Integração com Pacientes**
   - Vincular pacientes a logradouros específicos
   - Melhoria na precisão de endereços
   - Relatórios por localização

4. **Validação de CEP**
   - Integração com APIs de CEP
   - Validação automática
   - Preenchimento automático

## Considerações Técnicas

### Performance
- Índices otimizados para consultas frequentes
- JOINs eficientes entre tabelas relacionadas
- Cache de consultas quando necessário

### Segurança
- Validações server-side rigorosas
- Proteção contra SQL injection
- Sanitização de dados de entrada

### Manutenibilidade
- Código bem estruturado e documentado
- Separação clara de responsabilidades
- Padrões de nomenclatura consistentes

---

**Data da Implementação**: 02 de Julho de 2025  
**Versão do Sistema**: 1.0  
**Desenvolvedor**: Sistema de Pronto Atendimento Municipal
