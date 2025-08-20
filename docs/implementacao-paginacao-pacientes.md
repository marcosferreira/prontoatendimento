# Implementação da Paginação na Listagem de Pacientes

## Resumo da Implementação

Foi implementado um sistema de paginação completo na listagem de pacientes, permitindo navegar pelos registros de forma eficiente e manter a compatibilidade com o sistema de busca existente.

## Modificações Realizadas

### 1. PacienteModel (`app/Models/PacienteModel.php`)

#### Novos Métodos Adicionados:

```php
/**
 * Busca pacientes com seus logradouros com paginação
 */
public function getPacientesWithLogradouroPaginated($perPage = 20)
{
    $query = $this->select('pacientes.*, logradouros.nome_logradouro, logradouros.tipo_logradouro, logradouros.cep, logradouros.cidade, bairros.nome_bairro, bairros.area')
                  ->join('logradouros', 'logradouros.id_logradouro = pacientes.id_logradouro', 'left')
                  ->join('bairros', 'bairros.id_bairro = logradouros.id_bairro', 'left')
                  ->orderBy('pacientes.nome', 'ASC');

    $pacientes = $query->paginate($perPage);

    // Calcular idade para cada paciente se necessário
    foreach ($pacientes as &$paciente) {
        if (isset($paciente['data_nascimento'])) {
            $dataNascimento = new \DateTime($paciente['data_nascimento']);
            $hoje = new \DateTime();
            $paciente['idade'] = $hoje->diff($dataNascimento)->y;
        }
    }

    return $pacientes;
}

/**
 * Busca pacientes por nome ou CPF com paginação
 */
public function buscarPacientesPaginated($termo, $perPage = 20)
{
    $query = $this->select('pacientes.*, logradouros.nome_logradouro, logradouros.tipo_logradouro, logradouros.cep, logradouros.cidade, bairros.nome_bairro, bairros.area')
                  ->join('logradouros', 'logradouros.id_logradouro = pacientes.id_logradouro', 'left')
                  ->join('bairros', 'bairros.id_bairro = logradouros.id_bairro', 'left')
                  ->groupStart()
                      ->like('pacientes.nome', $termo)
                      ->orLike('pacientes.cpf', $termo)
                      ->orLike('pacientes.numero_sus', $termo)
                  ->groupEnd()
                  ->orderBy('pacientes.nome', 'ASC');

    $pacientes = $query->paginate($perPage);

    // Calcular idade para cada paciente se necessário
    foreach ($pacientes as &$paciente) {
        if (isset($paciente['data_nascimento'])) {
            $dataNascimento = new \DateTime($paciente['data_nascimento']);
            $hoje = new \DateTime();
            $paciente['idade'] = $hoje->diff($dataNascimento)->y;
        }
    }

    return $pacientes;
}
```

### 2. PacientesController (`app/Controllers/Pacientes.php`)

#### Método `index()` Modificado:

```php
public function index()
{
    $search = $this->request->getGet('search');
    $perPage = 20; // Definir quantos registros por página
    
    if ($search) {
        $pacientes = $this->pacienteModel->buscarPacientesPaginated($search, $perPage);
    } else {
        $pacientes = $this->pacienteModel->getPacientesWithLogradouroPaginated($perPage);
    }

    // Obter o objeto pager
    $pager = $this->pacienteModel->pager;

    // Estatísticas - criar novas instâncias do model para cada consulta
    $stats = [
        'total' => $this->pacienteModel->countAll(),
        'hoje' => $this->pacienteModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults(),
        'mes' => $this->pacienteModel->where('MONTH(created_at)', date('m'))
                                    ->where('YEAR(created_at)', date('Y'))
                                    ->countAllResults(),
        'idade_media' => round($this->pacienteModel->selectAvg('YEAR(CURDATE()) - YEAR(data_nascimento) - (DATE_FORMAT(CURDATE(), "%m%d") < DATE_FORMAT(data_nascimento, "%m%d"))', 'idade_media')
                                                    ->where('data_nascimento IS NOT NULL')
                                                    ->first()['idade_media'] ?? 0)
    ];

    $bairros = $this->bairroModel->findAll();
    $logradouros = $this->logradouroModel->select('logradouros.*, bairros.nome_bairro')
                                        ->join('bairros', 'bairros.id_bairro = logradouros.id_bairro')
                                        ->orderBy('bairros.nome_bairro', 'ASC')
                                        ->orderBy('logradouros.nome_logradouro', 'ASC')
                                        ->findAll();

    $data = [
        'title' => 'Pacientes',
        'description' => 'Gerenciar Pacientes',
        'pacientes' => $pacientes,
        'pager' => $pager,
        'stats' => $stats,
        'bairros' => $bairros,
        'logradouros' => $logradouros,
        'search' => $search
    ];

    return view('pacientes/index', $data);
}
```

### 3. View de Pacientes (`app/Views/pacientes/index.php`)

#### JavaScript Modificado:

A busca foi alterada de client-side para server-side para compatibilidade com paginação:

```javascript
// Busca de pacientes via servidor (compatível com paginação)
let searchTimeout;
document.getElementById('searchPaciente').addEventListener('keyup', function() {
    clearTimeout(searchTimeout);
    
    searchTimeout = setTimeout(() => {
        const searchTerm = this.value.trim();
        const currentUrl = new URL(window.location.href);
        
        if (searchTerm.length > 0) {
            currentUrl.searchParams.set('search', searchTerm);
        } else {
            currentUrl.searchParams.delete('search');
        }
        
        // Remove página atual para começar da primeira página na busca
        currentUrl.searchParams.delete('page');
        
        // Redirecionar para a URL com o termo de busca
        window.location.href = currentUrl.toString();
    }, 500); // Delay de 500ms para evitar muitas requisições
});

// Preservar termo de busca ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const searchTerm = urlParams.get('search');
    
    if (searchTerm) {
        document.getElementById('searchPaciente').value = searchTerm;
    }
});
```

## Funcionalidades Implementadas

### 1. Paginação Padrão
- **20 registros por página** (configurável)
- Navegação entre páginas através dos links de paginação
- Ordenação alfabética por nome do paciente

### 2. Busca com Paginação
- Busca por **nome**, **CPF** ou **número do SUS**
- Resultados paginados mantendo o termo de busca
- Delay de 500ms para evitar muitas requisições
- Reset automático para primeira página ao fazer nova busca

### 3. Preservação de Estado
- Termo de busca preservado na URL e no campo de entrada
- Navegação entre páginas mantém os filtros aplicados
- Links de paginação respeitam parâmetros de busca

### 4. Estatísticas Corrigidas
- Total de pacientes: `countAll()`
- Cadastrados hoje: filtro por data de criação
- Cadastrados no mês: filtro por mês/ano
- Idade média: cálculo baseado na data de nascimento

## Melhorias de Performance

### 1. Paginação no Banco
- Consultas limitadas pelo `LIMIT` e `OFFSET` automáticos
- Redução significativa na transferência de dados
- Melhor responsividade em bases grandes

### 2. Busca Otimizada
- Filtros aplicados diretamente na query SQL
- Uso de `LIKE` para busca flexível
- Junções otimizadas com logradouros e bairros

### 3. JavaScript Eficiente
- Debounce de 500ms para reduzir requisições
- Redirecionamento direto sem AJAX desnecessário
- Preservação de estado através de URL parameters

## URLs de Exemplo

```
# Página inicial
/pacientes

# Segunda página
/pacientes?page=2

# Busca por "João"
/pacientes?search=João

# Busca paginada
/pacientes?search=João&page=2
```

## Configuração

### Alterar Itens Por Página
Para alterar o número de registros por página, modifique a variável `$perPage` no controller:

```php
$perPage = 20; // Altere este valor conforme necessário
```

### Personalizar Paginação
O CodeIgniter 4 permite personalizar os templates de paginação no arquivo de configuração `app/Config/Pager.php`.

## Compatibilidade

### Retrocompatibilidade
- Métodos antigos (`getPacientesWithLogradouro()` e `buscarPacientes()`) foram mantidos
- Views existentes continuam funcionando
- URLs sem paginação redirecionam para a primeira página

### Funcionalidades Mantidas
- Modais de visualização, edição e exclusão
- Validações de CPF e formulários
- Sistema de endereços locais e externos
- Estatísticas e cards informativos

## Testagem

Para testar a implementação:

1. **Acesse a página de pacientes**: `/pacientes`
2. **Teste a paginação**: Clique nos números das páginas
3. **Teste a busca**: Digite um nome, CPF ou SUS no campo de busca
4. **Teste a navegação**: Navegue entre páginas mantendo a busca ativa
5. **Verifique as estatísticas**: Confirme se os números estão corretos

## Benefícios da Implementação

- ✅ **Performance**: Carregamento mais rápido com grandes volumes de dados
- ✅ **Usabilidade**: Navegação intuitiva entre páginas
- ✅ **Busca Eficiente**: Resultados paginados para facilitar a localização
- ✅ **URL Amigável**: Links compartilháveis com estado preservado
- ✅ **Responsividade**: Interface mantém boa performance mesmo com muitos pacientes
- ✅ **Compatibilidade**: Funciona com sistema de busca e filtros existentes
