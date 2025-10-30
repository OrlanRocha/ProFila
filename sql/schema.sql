-- ProFila Schema
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

CREATE TABLE filas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  sigla VARCHAR(5) NOT NULL,
  prioridade_padrao INT NOT NULL DEFAULT 0,
  sequencial_atual INT NOT NULL DEFAULT 0,
  ativo TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE guiches (
  id INT AUTO_INCREMENT PRIMARY KEY,
  numero INT NOT NULL,
  apelido VARCHAR(60) NULL,
  fila_padrao_id INT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT fk_guiche_fila FOREIGN KEY (fila_padrao_id) REFERENCES filas(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE senhas (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(12) NOT NULL,
  fila_id INT NOT NULL,
  prioridade INT NOT NULL DEFAULT 0,
  status ENUM('aguardando','chamada','em_atendimento','finalizada','descartada') NOT NULL DEFAULT 'aguardando',
  guiche_id INT NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  chamado_em DATETIME NULL,
  atualizado_em DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_senha_fila FOREIGN KEY (fila_id) REFERENCES filas(id),
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

INSERT INTO usuarios (nome, email, senha_hash, papel) VALUES
('Admin', 'admin@local', '$2y$12$BiIwrOx.S/Y6xhKfyw.X7O2NnSsrhC0BBLYqucjO8rmsSmAj6keDC', 'admin');

INSERT INTO filas (nome, sigla, prioridade_padrao, sequencial_atual, ativo) VALUES
('Geral', 'A', 0, 0, 1),
('Prioridade', 'P', 10, 0, 1);

INSERT INTO guiches (numero, apelido, fila_padrao_id, ativo) VALUES
(1, 'Guichê 1', 1, 1),
(2, 'Guichê 2', 1, 1);
