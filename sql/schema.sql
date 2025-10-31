-- ProFila Schema Modernizado
CREATE TABLE orgaos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(160) NOT NULL,
  sigla VARCHAR(20) NOT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(160) NOT NULL,
  documento VARCHAR(32) NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE uo_entidades (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nivel ENUM('I','II','III') NOT NULL,
  nome VARCHAR(160) NOT NULL,
  codigo VARCHAR(40) NOT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE unidades (
  id INT AUTO_INCREMENT PRIMARY KEY,
  orgao_id INT NOT NULL,
  cliente_id INT NOT NULL,
  nome VARCHAR(160) NOT NULL,
  codigo VARCHAR(32) NOT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  uo_nivel_i_id INT NULL,
  uo_nivel_ii_id INT NULL,
  uo_nivel_iii_id INT NULL,
  CONSTRAINT fk_unidade_orgao FOREIGN KEY (orgao_id) REFERENCES orgaos(id),
  CONSTRAINT fk_unidade_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id),
  CONSTRAINT fk_unidade_uo_i FOREIGN KEY (uo_nivel_i_id) REFERENCES uo_entidades(id),
  CONSTRAINT fk_unidade_uo_ii FOREIGN KEY (uo_nivel_ii_id) REFERENCES uo_entidades(id),
  CONSTRAINT fk_unidade_uo_iii FOREIGN KEY (uo_nivel_iii_id) REFERENCES uo_entidades(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE servico_categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(160) NOT NULL,
  descricao TEXT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE servicos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  categoria_id INT NULL,
  nome VARCHAR(160) NOT NULL,
  descricao TEXT NULL,
  duracao_minutos INT NOT NULL DEFAULT 0,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_servico_categoria FOREIGN KEY (categoria_id) REFERENCES servico_categorias(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE agendamentos (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(160) NOT NULL,
  documento VARCHAR(40) NULL,
  contato VARCHAR(80) NULL,
  prioridade_tipo ENUM('padrao','preferencial','80+','servico') NOT NULL DEFAULT 'padrao',
  categoria VARCHAR(120) NULL,
  servico_id INT NULL,
  unidade_id INT NULL,
  data_agendada DATE NULL,
  hora_agendada TIME NULL,
  observacoes TEXT NULL,
  origem VARCHAR(30) NOT NULL DEFAULT 'interno',
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_agendamento_servico FOREIGN KEY (servico_id) REFERENCES servicos(id),
  CONSTRAINT fk_agendamento_unidade FOREIGN KEY (unidade_id) REFERENCES unidades(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  papel ENUM('admin','gestor','atendente','visor') NOT NULL DEFAULT 'atendente',
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  ultimo_login DATETIME NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE permissoes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  chave VARCHAR(80) NOT NULL UNIQUE,
  nome VARCHAR(160) NOT NULL,
  descricao TEXT NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE papel_permissoes (
  papel ENUM('admin','gestor','atendente','visor') NOT NULL,
  permissao_id INT NOT NULL,
  permitido TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (papel, permissao_id),
  CONSTRAINT fk_pp_permissao FOREIGN KEY (permissao_id) REFERENCES permissoes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE filas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  unidade_id INT NULL,
  nome VARCHAR(120) NOT NULL,
  sigla VARCHAR(5) NOT NULL,
  prioridade_padrao INT NOT NULL DEFAULT 0,
  sequencial_atual INT NOT NULL DEFAULT 0,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT fk_fila_unidade FOREIGN KEY (unidade_id) REFERENCES unidades(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE guiches (
  id INT AUTO_INCREMENT PRIMARY KEY,
  unidade_id INT NULL,
  numero INT NOT NULL,
  apelido VARCHAR(60) NULL,
  fila_padrao_id INT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  modo_atendimento ENUM('fifo','sequencial') NOT NULL DEFAULT 'fifo',
  prioridades_config JSON NOT NULL DEFAULT (JSON_ARRAY('padrao')),
  CONSTRAINT fk_guiche_fila FOREIGN KEY (fila_padrao_id) REFERENCES filas(id),
  CONSTRAINT fk_guiche_unidade FOREIGN KEY (unidade_id) REFERENCES unidades(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE guiche_usuarios (
  guiche_id INT NOT NULL,
  usuario_id INT NOT NULL,
  perfil VARCHAR(80) NOT NULL,
  PRIMARY KEY (guiche_id, usuario_id),
  CONSTRAINT fk_guiche_usuario_guiche FOREIGN KEY (guiche_id) REFERENCES guiches(id) ON DELETE CASCADE,
  CONSTRAINT fk_guiche_usuario_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE painel_regras (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  descricao TEXT NULL,
  configuracao JSON NOT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE painel_displays (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ip_address VARCHAR(45) NOT NULL,
  token CHAR(36) NOT NULL,
  apelido VARCHAR(120) NULL,
  unidade_id INT NULL,
  regra_id INT NULL,
  status ENUM('pendente','ativo','inativo') NOT NULL DEFAULT 'pendente',
  ultimo_visto DATETIME NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_ip (ip_address),
  UNIQUE KEY uniq_token (token),
  CONSTRAINT fk_display_unidade FOREIGN KEY (unidade_id) REFERENCES unidades(id),
  CONSTRAINT fk_display_regra FOREIGN KEY (regra_id) REFERENCES painel_regras(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE senhas (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(12) NOT NULL,
  fila_id INT NOT NULL,
  unidade_id INT NULL,
  prioridade INT NOT NULL DEFAULT 0,
  prioridade_tipo ENUM('padrao','preferencial','80+','servico') NOT NULL DEFAULT 'padrao',
  status ENUM('aguardando','chamada','em_atendimento','finalizada','descartada') NOT NULL DEFAULT 'aguardando',
  guiche_id INT NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  chamado_em DATETIME NULL,
  atualizado_em DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_senha_fila FOREIGN KEY (fila_id) REFERENCES filas(id),
  CONSTRAINT fk_senha_unidade FOREIGN KEY (unidade_id) REFERENCES unidades(id),
  CONSTRAINT fk_senha_guiche FOREIGN KEY (guiche_id) REFERENCES guiches(id),
  INDEX idx_senha_ordem (status, prioridade DESC, criado_em ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE atendimentos (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  senha_id BIGINT NOT NULL,
  atendente_id INT NULL,
  inicio DATETIME NULL,
  fim DATETIME NULL,
  observacoes TEXT NULL,
  CONSTRAINT fk_at_senha FOREIGN KEY (senha_id) REFERENCES senhas(id),
  CONSTRAINT fk_at_user FOREIGN KEY (atendente_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE logs_eventos (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  tipo VARCHAR(40) NOT NULL,
  referencia_id BIGINT NULL,
  payload JSON NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_logs_tipo (tipo),
  INDEX idx_logs_criado (criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO orgaos (nome, sigla) VALUES
('Secretaria de Atendimento', 'SECAT');

INSERT INTO clientes (nome, documento) VALUES
('Prefeitura Municipal', '11.222.333/0001-44');

INSERT INTO uo_entidades (nivel, nome, codigo) VALUES
('I', 'Secretaria Geral', 'UO-I-001'),
('II', 'Coordenadoria Metropolitana', 'UO-II-002'),
('III', 'Posto Central', 'UO-III-003');

INSERT INTO servico_categorias (nome, descricao) VALUES
('Documentos', 'Emissão e regularização de documentos civis'),
('Veículos', 'Serviços relacionados a licenciamento e CNH');

INSERT INTO servicos (categoria_id, nome, descricao, duracao_minutos) VALUES
(1, 'RG - 1ª via', 'Primeira via do documento de identidade', 20),
(1, 'RG - 2ª via', 'Reemissão de documento de identidade', 15),
(2, 'Renovação de CNH', 'Renovação da carteira de motorista', 25);

INSERT INTO unidades (orgao_id, cliente_id, nome, codigo, uo_nivel_i_id, uo_nivel_ii_id, uo_nivel_iii_id) VALUES
(1, 1, 'Unidade Central', 'UO-CENTRAL', 1, 2, 3);

INSERT INTO usuarios (nome, email, senha_hash, papel) VALUES
('Admin', 'admin@local', '$2y$12$BiIwrOx.S/Y6xhKfyw.X7O2NnSsrhC0BBLYqucjO8rmsSmAj6keDC', 'admin');

INSERT INTO permissoes (chave, nome, descricao) VALUES
('dashboard.view', 'Visualizar dashboard', 'Permite acessar a página inicial do sistema com indicadores.'),
('usuarios.manage', 'Gerenciar usuários', 'Criar, editar e desativar contas de usuário.'),
('filas.manage', 'Gerenciar filas', 'Criar e organizar filas de atendimento por unidade.'),
('guiches.manage', 'Gerenciar guichês', 'Configurar mesas, prioridades e atribuições.'),
('senhas.emit', 'Emitir senhas', 'Liberar senhas para o público.'),
('senhas.operate', 'Operar guichê', 'Chamar, rechamar e finalizar senhas.'),
('painel.view', 'Exibir painel público', 'Acessar as telas de painel público.'),
('painel.manage', 'Gerenciar paineis', 'Configurar displays e regras por unidade.'),
('relatorios.view', 'Visualizar relatórios', 'Acessar gráficos e relatórios analíticos.'),
('permissoes.manage', 'Gerenciar permissões', 'Ajustar permissões de cada perfil de usuário.');

INSERT INTO painel_regras (nome, descricao, configuracao) VALUES
('Padrão', 'Mostra últimas senhas chamadas por unidade.', JSON_OBJECT('historico', 3, 'tema', 'claro')),
('Alta Rotatividade', 'Atualização agressiva para unidades com grande fluxo.', JSON_OBJECT('historico', 5, 'tema', 'escuro'));

INSERT INTO painel_displays (ip_address, token, apelido, unidade_id, regra_id, status)
VALUES ('0.0.0.0', UUID(), 'Painel Padrão', 1, 1, 'ativo');

INSERT INTO filas (unidade_id, nome, sigla, prioridade_padrao, sequencial_atual, ativo) VALUES
(1, 'Atendimento Geral', 'A', 0, 0, 1),
(1, 'Atendimento Preferencial', 'P', 10, 0, 1);

INSERT INTO guiches (unidade_id, numero, apelido, fila_padrao_id, ativo, modo_atendimento, prioridades_config) VALUES
(1, 1, 'Guichê 1', 1, 1, 'fifo', JSON_ARRAY('padrao','preferencial')),
(1, 2, 'Guichê 2', 1, 1, 'sequencial', JSON_ARRAY('padrao'));

INSERT INTO guiche_usuarios (guiche_id, usuario_id, perfil) VALUES
(1, 1, 'supervisor');

INSERT INTO agendamentos (nome, documento, contato, prioridade_tipo, categoria, servico_id, unidade_id, data_agendada, hora_agendada, origem)
VALUES ('Maria Ferreira', '123.456.789-00', '(11) 99999-0000', 'preferencial', 'Documentos', 1, 1, CURDATE(), '10:30', 'interno');

INSERT INTO papel_permissoes (papel, permissao_id, permitido)
SELECT 'admin', id, 1 FROM permissoes;

INSERT INTO papel_permissoes (papel, permissao_id, permitido)
SELECT 'gestor', id, CASE WHEN chave IN ('usuarios.manage','permissoes.manage') THEN 0 ELSE 1 END FROM permissoes;

INSERT INTO papel_permissoes (papel, permissao_id, permitido)
SELECT 'atendente', id, CASE WHEN chave IN ('senhas.emit','senhas.operate','painel.view') THEN 1 ELSE 0 END FROM permissoes;

INSERT INTO papel_permissoes (papel, permissao_id, permitido)
SELECT 'visor', id, CASE WHEN chave IN ('painel.view') THEN 1 ELSE 0 END FROM permissoes;
