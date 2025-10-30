-- ProFila - Dados de exemplo para testes manuais e automatizados
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE logs_eventos;
TRUNCATE TABLE atendimentos;
TRUNCATE TABLE senhas;
TRUNCATE TABLE guiches;
TRUNCATE TABLE filas;
TRUNCATE TABLE usuarios;
SET FOREIGN_KEY_CHECKS = 1;

-- Usuários de teste (senhas: admin123, gestor123, atendente123, visor123)
INSERT INTO usuarios (nome, email, senha_hash, papel, ativo, ultimo_login, criado_em) VALUES
('Administrador', 'admin@local', '$2y$12$mjLlwGdIiW4UQp3mhi0QxOyrA25We4fcEOhWvjdS2vQiudvGdUctm', 'admin', 1, '2025-01-10 08:00:00', '2025-01-02 09:00:00'),
('Maria Gestora', 'gestor@local', '$2y$12$75tkpOQN.ialEB7b4yHpLurRQaanbLBZOh9cPmUA3OQkGyZhIYVpe', 'gestor', 1, '2025-01-10 08:10:00', '2025-01-03 10:00:00'),
('Carlos Atendente', 'atendente@local', '$2y$12$0hspJq1wOpQEtp2fHZ4FPOjv4153H9BgRKBy8Wmm38uFsK6NqLRxy', 'atendente', 1, '2025-01-10 08:45:00', '2025-01-04 11:00:00'),
('Paula Visor', 'visor@local', '$2y$12$5OR7lWnnr1PnIbE.19GVAe/iTz47QH6aUpBcKm4QWVdxsHlJ5Luzi', 'visor', 1, NULL, '2025-01-05 12:00:00');

-- Filas disponíveis
INSERT INTO filas (nome, sigla, prioridade_padrao, sequencial_atual, ativo) VALUES
('Geral', 'A', 0, 27, 1),
('Prioridade', 'P', 10, 12, 1),
('Documentos', 'D', 0, 8, 1);

-- Guichês configurados
INSERT INTO guiches (numero, apelido, fila_padrao_id, ativo) VALUES
(1, 'Central 1', 1, 1),
(2, 'Central 2', 1, 1),
(3, 'Atendimento Prioritário', 2, 1),
(4, 'Documentos', 3, 0);

-- Senhas emitidas recentemente
INSERT INTO senhas (codigo, fila_id, prioridade, status, guiche_id, criado_em, chamado_em, atualizado_em) VALUES
('A024', 1, 0, 'aguardando', NULL, '2025-01-10 09:05:00', NULL, NULL),
('A025', 1, 0, 'aguardando', NULL, '2025-01-10 09:06:30', NULL, NULL),
('A023', 1, 0, 'chamada', 1, '2025-01-10 09:00:00', '2025-01-10 09:03:15', '2025-01-10 09:03:15'),
('A022', 1, 0, 'em_atendimento', 2, '2025-01-10 08:55:00', '2025-01-10 08:57:30', '2025-01-10 08:57:45'),
('P011', 2, 10, 'finalizada', 3, '2025-01-10 08:30:00', '2025-01-10 08:31:00', '2025-01-10 08:38:40'),
('P010', 2, 10, 'descartada', 3, '2025-01-10 08:20:00', '2025-01-10 08:22:10', '2025-01-10 08:50:00'),
('D007', 3, 0, 'finalizada', 4, '2025-01-09 15:00:00', '2025-01-09 15:05:00', '2025-01-09 15:12:10'),
('P012', 2, 10, 'aguardando', NULL, '2025-01-10 09:07:00', NULL, NULL);

-- Atendimentos relacionados às senhas
INSERT INTO atendimentos (senha_id, atendente_id, inicio, fim, observacoes) VALUES
(4, 3, '2025-01-10 08:57:45', NULL, 'Cliente ainda em atendimento'),
(5, 3, '2025-01-10 08:31:05', '2025-01-10 08:38:40', 'Solicitação resolvida sem pendências'),
(7, 3, '2025-01-09 15:05:30', '2025-01-09 15:12:10', 'Entrega de documentação concluída');

-- Logs de eventos detalhados
INSERT INTO logs_eventos (tipo, referencia_id, payload, criado_em) VALUES
('usuario_login', 2, '{"usuario":"Maria Gestora","ip":"127.0.0.1"}', '2025-01-10 08:10:05'),
('senha_emitida', 1, '{"codigo":"A024","fila":"Geral","prioridade":0,"origem":"totem"}', '2025-01-10 09:05:00'),
('senha_emitida', 2, '{"codigo":"A025","fila":"Geral","prioridade":0,"origem":"balcao"}', '2025-01-10 09:06:30'),
('senha_chamada', 3, '{"codigo":"A023","guiche":1,"atendente":"Carlos Atendente"}', '2025-01-10 09:03:15'),
('senha_em_atendimento', 4, '{"codigo":"A022","guiche":2,"tempo_espera_segundos":150}', '2025-01-10 08:57:45'),
('senha_rechamada', 3, '{"codigo":"A023","guiche":1,"contador":2}', '2025-01-10 09:04:10'),
('senha_finalizada', 5, '{"codigo":"P011","guiche":3,"duracao_segundos":455}', '2025-01-10 08:38:45'),
('senha_transferida', 6, '{"codigo":"P010","origem_fila":"Prioridade","destino_fila":"Documentos"}', '2025-01-10 08:45:00'),
('senha_descartada', 6, '{"codigo":"P010","motivo":"nao_compareceu"}', '2025-01-10 08:50:00');
