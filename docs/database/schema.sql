# Script DDL - Criação das Tabelas do Banco de Dados

```sql
-- =============================================
-- Script de Criação do Banco de Dados
-- Pronto Atendimento Municipal
-- =============================================

-- Criação da tabela BAIRRO
CREATE TABLE bairro (
    id_bairro SERIAL PRIMARY KEY,
    nome_bairro VARCHAR(100) NOT NULL,
    area VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Criação da tabela PACIENTE
CREATE TABLE paciente (
    id_paciente SERIAL PRIMARY KEY,
    nome VARCHAR(200) NOT NULL,
    sus VARCHAR(20),
    cpf VARCHAR(14) UNIQUE,
    endereco TEXT,
    id_bairro INTEGER REFERENCES bairro(id_bairro),
    data_nascimento DATE,
    idade INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Criação da tabela MEDICO
CREATE TABLE medico (
    id_medico SERIAL PRIMARY KEY,
    nome VARCHAR(200) NOT NULL,
    crm VARCHAR(20) UNIQUE NOT NULL,
    especialidade VARCHAR(100),
    status VARCHAR(20) DEFAULT 'ATIVO' CHECK (status IN ('ATIVO', 'INATIVO')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Criação da tabela ATENDIMENTO
CREATE TABLE atendimento (
    id_atendimento SERIAL PRIMARY KEY,
    id_paciente INTEGER NOT NULL REFERENCES paciente(id_paciente),
    id_medico INTEGER REFERENCES medico(id_medico),
    data_atendimento TIMESTAMP NOT NULL,
    classificacao_risco VARCHAR(20) CHECK (classificacao_risco IN ('VERDE', 'AMARELO', 'VERMELHO', 'AZUL')),
    consulta_enfermagem TEXT,
    hgt_glicemia VARCHAR(20),
    pressao_arterial VARCHAR(20),
    hipotese_diagnostico TEXT,
    observacao TEXT,
    encaminhamento VARCHAR(200),
    obito BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Criação da tabela PROCEDIMENTO
CREATE TABLE procedimento (
    id_procedimento SERIAL PRIMARY KEY,
    nome VARCHAR(200) NOT NULL,
    codigo VARCHAR(50),
    descricao TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Criação da tabela EXAME
CREATE TABLE exame (
    id_exame SERIAL PRIMARY KEY,
    nome VARCHAR(200) NOT NULL,
    codigo VARCHAR(50),
    tipo VARCHAR(50),
    descricao TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Criação da tabela de relacionamento ATENDIMENTO_PROCEDIMENTO
CREATE TABLE atendimento_procedimento (
    id_atendimento_procedimento SERIAL PRIMARY KEY,
    id_atendimento INTEGER NOT NULL REFERENCES atendimento(id_atendimento),
    id_procedimento INTEGER NOT NULL REFERENCES procedimento(id_procedimento),
    quantidade INTEGER DEFAULT 1,
    observacao TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Criação da tabela de relacionamento ATENDIMENTO_EXAME
CREATE TABLE atendimento_exame (
    id_atendimento_exame SERIAL PRIMARY KEY,
    id_atendimento INTEGER NOT NULL REFERENCES atendimento(id_atendimento),
    id_exame INTEGER NOT NULL REFERENCES exame(id_exame),
    resultado TEXT,
    status VARCHAR(20) DEFAULT 'SOLICITADO' CHECK (status IN ('SOLICITADO', 'REALIZADO', 'CANCELADO')),
    data_solicitacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_realizacao TIMESTAMP,
    observacao TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- CRIAÇÃO DE ÍNDICES
-- =============================================

-- Índices de performance
CREATE UNIQUE INDEX idx_paciente_cpf ON paciente(cpf);
CREATE INDEX idx_paciente_sus ON paciente(sus);
CREATE INDEX idx_atendimento_data ON atendimento(data_atendimento);
CREATE INDEX idx_atendimento_paciente ON atendimento(id_paciente);
CREATE INDEX idx_atendimento_medico ON atendimento(id_medico);
CREATE UNIQUE INDEX idx_medico_crm ON medico(crm);

-- Índices de busca
CREATE INDEX idx_paciente_nome ON paciente(nome);
CREATE INDEX idx_bairro_nome ON bairro(nome_bairro);
CREATE INDEX idx_atendimento_classificacao ON atendimento(classificacao_risco);

-- Índices para tabelas de relacionamento
CREATE INDEX idx_atendimento_procedimento_atendimento ON atendimento_procedimento(id_atendimento);
CREATE INDEX idx_atendimento_procedimento_procedimento ON atendimento_procedimento(id_procedimento);
CREATE INDEX idx_atendimento_exame_atendimento ON atendimento_exame(id_atendimento);
CREATE INDEX idx_atendimento_exame_exame ON atendimento_exame(id_exame);

-- =============================================
-- CRIAÇÃO DE TRIGGERS
-- =============================================

-- Trigger para atualizar o campo updated_at automaticamente
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Aplicar trigger nas tabelas que possuem updated_at
CREATE TRIGGER update_paciente_updated_at BEFORE UPDATE ON paciente
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_medico_updated_at BEFORE UPDATE ON medico
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_atendimento_updated_at BEFORE UPDATE ON atendimento
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- Trigger para calcular idade automaticamente
CREATE OR REPLACE FUNCTION calculate_age()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.data_nascimento IS NOT NULL THEN
        NEW.idade = EXTRACT(YEAR FROM AGE(NEW.data_nascimento));
    END IF;
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER calculate_paciente_age BEFORE INSERT OR UPDATE ON paciente
    FOR EACH ROW EXECUTE FUNCTION calculate_age();

-- =============================================
-- DADOS INICIAIS (SEED DATA)
-- =============================================

-- Inserir alguns bairros padrão
INSERT INTO bairro (nome_bairro, area) VALUES
('Centro', 'Área Central'),
('Vila Nova', 'Zona Norte'),
('Jardim Primavera', 'Zona Sul'),
('Industrial', 'Zona Leste'),
('Santa Rosa', 'Zona Oeste');

-- Inserir classificações e códigos de procedimentos comuns
INSERT INTO procedimento (nome, codigo, descricao) VALUES
('Consulta Médica', 'CONS001', 'Consulta médica de pronto atendimento'),
('Curativo Simples', 'PROC001', 'Realização de curativo simples'),
('Sutura', 'PROC002', 'Sutura de ferimento'),
('Medicação Intramuscular', 'PROC003', 'Aplicação de medicação via intramuscular'),
('Medicação Endovenosa', 'PROC004', 'Aplicação de medicação via endovenosa');

-- Inserir exames comuns
INSERT INTO exame (nome, codigo, tipo, descricao) VALUES
('Hemograma Completo', 'LAB001', 'Laboratorial', 'Exame de sangue completo'),
('Glicemia', 'LAB002', 'Laboratorial', 'Dosagem de glicose sanguínea'),
('Raio-X Tórax', 'IMG001', 'Imagem', 'Radiografia do tórax'),
('ECG', 'CARD001', 'Cardiológico', 'Eletrocardiograma'),
('Urina Tipo I', 'LAB003', 'Laboratorial', 'Exame de urina rotina');
```
