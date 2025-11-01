-- Seeds avançados para ProFila
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE logs_eventos;
TRUNCATE TABLE atendimentos;
TRUNCATE TABLE senhas;
TRUNCATE TABLE guiche_usuarios;
TRUNCATE TABLE guiches;
TRUNCATE TABLE filas;
TRUNCATE TABLE painel_displays;
TRUNCATE TABLE painel_regras;
TRUNCATE TABLE agendamentos;
TRUNCATE TABLE servicos;
TRUNCATE TABLE servico_categorias;
TRUNCATE TABLE papel_permissoes;
TRUNCATE TABLE permissoes;
TRUNCATE TABLE usuarios;
TRUNCATE TABLE uo_entidades;
TRUNCATE TABLE unidades;
TRUNCATE TABLE clientes;
TRUNCATE TABLE orgaos;
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO orgaos (id, nome, sigla, ativo) VALUES
(1, 'Secretaria de Atendimento', 'SECAT', 1),
(2, 'Secretaria de Saúde', 'SAUDE', 1);

INSERT INTO clientes (id, nome, documento, ativo) VALUES
(1, 'Prefeitura Municipal', '11.222.333/0001-44', 1),
(2, 'Câmara Municipal', '55.666.777/0001-99', 1);

INSERT INTO uo_entidades (id, nivel, nome, codigo, ativo) VALUES
(1, 'I', 'Secretaria Geral', 'UO-I-001', 1),
(2, 'II', 'Coordenadoria Metropolitana', 'UO-II-002', 1),
(3, 'III', 'Posto Central', 'UO-III-003', 1),
(4, 'III', 'Posto Norte', 'UO-III-004', 1);

INSERT INTO unidades (id, orgao_id, cliente_id, nome, codigo, ativo, uo_nivel_i_id, uo_nivel_ii_id, uo_nivel_iii_id) VALUES
(1, 1, 1, 'Unidade Central', 'UO-CENTRAL', 1, 1, 2, 3),
(2, 1, 1, 'Unidade Norte', 'UO-NORTE', 1, 1, 2, 4),
(3, 2, 2, 'Posto de Saúde 1', 'UO-SA1', 1, NULL, NULL, NULL);

INSERT INTO servico_categorias (id, nome, descricao, ativo) VALUES
(1, 'Documentos', 'Emissão e regularização de documentos civis', 1),
(2, 'Veículos', 'Serviços relacionados a CNH e licenciamento', 1);

INSERT INTO servicos (id, categoria_id, nome, descricao, duracao_minutos, ativo) VALUES
(1, 1, 'RG - 1ª via', 'Primeira via do documento de identidade', 20, 1),
(2, 1, 'RG - 2ª via', 'Reemissão de documento de identidade', 15, 1),
(3, 2, 'Renovação de CNH', 'Processo completo de renovação', 25, 1);

INSERT INTO usuarios (id, nome, email, senha_hash, papel, ativo) VALUES
(1, 'Ana Ribeiro', 'ana@prefeitura.local', '$2y$12$BiIwrOx.S/Y6xhKfyw.X7O2NnSsrhC0BBLYqucjO8rmsSmAj6keDC', 'admin', 1),
(2, 'Carlos Mendes', 'carlos@prefeitura.local', '$2y$12$Hwm1jz0WJZXniTOW8g4g1eUhiZfLwSSmRc/OPJuc/qCaQSicp8MOO', 'gestor', 1),
(3, 'Fernanda Lima', 'fernanda@prefeitura.local', '$2y$12$E22oY2iAZVKPrcuhtpBaiu7KCp9XScpZ6vxvL6PpZsCfZcbLZk9Si', 'atendente', 1),
(4, 'João Souza', 'joao@prefeitura.local', '$2y$12$E22oY2iAZVKPrcuhtpBaiu7KCp9XScpZ6vxvL6PpZsCfZcbLZk9Si', 'atendente', 1),
(5, 'Painel Recepção', 'painel@prefeitura.local', '$2y$12$BiIwrOx.S/Y6xhKfyw.X7O2NnSsrhC0BBLYqucjO8rmsSmAj6keDC', 'visor', 1);

INSERT INTO permissoes (id, chave, nome, descricao) VALUES
(1, 'dashboard.view', 'Visualizar dashboard', 'Permite acessar a página inicial do sistema com indicadores.'),
(2, 'usuarios.manage', 'Gerenciar usuários', 'Criar, editar e desativar contas de usuário.'),
(3, 'filas.manage', 'Gerenciar filas', 'Criar e organizar filas de atendimento por unidade.'),
(4, 'guiches.manage', 'Gerenciar guichês', 'Configurar mesas, prioridades e atribuições.'),
(5, 'senhas.emit', 'Emitir senhas', 'Liberar senhas para o público.'),
(6, 'senhas.operate', 'Operar guichê', 'Chamar, rechamar e finalizar senhas.'),
(7, 'painel.view', 'Exibir painel público', 'Acessar as telas de painel público.'),
(8, 'painel.manage', 'Gerenciar paineis', 'Configurar displays e regras por unidade.'),
(9, 'relatorios.view', 'Visualizar relatórios', 'Acessar gráficos e relatórios analíticos.'),
(10, 'permissoes.manage', 'Gerenciar permissões', 'Ajustar permissões de cada perfil de usuário.');

INSERT INTO painel_regras (id, nome, descricao, configuracao, ativo) VALUES
(1, 'Padrão', 'Mostra últimas senhas chamadas por unidade.', JSON_OBJECT('historico', 3, 'tema', 'claro'), 1),
(2, 'Alta Rotatividade', 'Atualização agressiva para unidades com grande fluxo.', JSON_OBJECT('historico', 5, 'tema', 'escuro', 'polling', 2000), 1);

INSERT INTO painel_displays (id, ip_address, token, apelido, unidade_id, regra_id, status, ultimo_visto) VALUES
(1, '10.0.0.10', UUID(), 'Painel Recepção', 1, 1, 'ativo', NOW()),
(2, '10.0.0.11', UUID(), 'Painel Prioritário', 1, 2, 'ativo', NOW()),
(3, '10.0.0.30', UUID(), NULL, NULL, NULL, 'pendente', NULL);

INSERT INTO filas (id, unidade_id, nome, sigla, prioridade_padrao, sequencial_atual, ativo) VALUES
(1, 1, 'Atendimento Geral', 'A', 0, 36, 1),
(2, 1, 'Atendimento Prioritário', 'P', 10, 22, 1),
(3, 2, 'Serviços Regionais', 'S', 0, 11, 1),
(4, 3, 'Saúde - Geral', 'H', 0, 18, 1);

INSERT INTO guiches (id, unidade_id, numero, apelido, fila_padrao_id, ativo, modo_atendimento, prioridades_config) VALUES
(1, 1, 1, 'Guichê Central 1', 1, 1, 'fifo', JSON_ARRAY('padrao','preferencial')),
(2, 1, 2, 'Guichê Central 2', 1, 1, 'sequencial', JSON_ARRAY('padrao')),
(3, 1, 3, 'Guichê Prioridade', 2, 1, 'fifo', JSON_ARRAY('preferencial','80+')),
(4, 2, 1, 'Guichê Norte', 3, 1, 'fifo', JSON_ARRAY('padrao','servico')),
(5, 3, 1, 'Guichê Saúde', 4, 1, 'fifo', JSON_ARRAY('padrao','preferencial'));

INSERT INTO guiche_usuarios (guiche_id, usuario_id, perfil) VALUES
(1, 3, 'atendente_junior'),
(2, 4, 'atendente'),
(3, 3, 'prioritario'),
(5, 4, 'saude_preferencial');

INSERT INTO agendamentos (id, nome, documento, contato, prioridade_tipo, categoria, servico_id, unidade_id, data_agendada, hora_agendada, origem) VALUES
(1, 'Maria Ferreira', '123.456.789-00', '(11) 99999-0000', 'preferencial', 'Documentos', 1, 1, CURDATE(), '10:30', 'interno'),
(2, 'João Mendes', '987.654.321-00', '(11) 98888-1212', 'padrao', 'Veículos', 3, 2, CURDATE() + INTERVAL 1 DAY, '11:00', 'totem');

INSERT INTO papel_permissoes (papel, permissao_id, permitido) VALUES
('admin', 1, 1),('admin', 2, 1),('admin', 3, 1),('admin', 4, 1),('admin', 5, 1),('admin', 6, 1),('admin', 7, 1),('admin', 8, 1),('admin', 9, 1),('admin', 10, 1),
('gestor', 1, 1),('gestor', 2, 1),('gestor', 3, 1),('gestor', 4, 1),('gestor', 5, 1),('gestor', 6, 1),('gestor', 7, 1),('gestor', 8, 1),('gestor', 9, 1),('gestor', 10, 0),
('atendente', 1, 0),('atendente', 2, 0),('atendente', 3, 0),('atendente', 4, 0),('atendente', 5, 1),('atendente', 6, 1),('atendente', 7, 1),('atendente', 8, 0),('atendente', 9, 0),('atendente', 10, 0),
('visor', 1, 0),('visor', 2, 0),('visor', 3, 0),('visor', 4, 0),('visor', 5, 0),('visor', 6, 0),('visor', 7, 1),('visor', 8, 0),('visor', 9, 0),('visor', 10, 0);

INSERT INTO senhas (id, codigo, fila_id, unidade_id, prioridade, prioridade_tipo, status, guiche_id, criado_em, chamado_em) VALUES
(1, 'A001', 1, 1, 0, 'padrao', 'finalizada', 1, NOW() - INTERVAL 90 MINUTE, NOW() - INTERVAL 80 MINUTE),
(2, 'A037', 1, 1, 0, 'padrao', 'em_atendimento', 2, NOW() - INTERVAL 5 MINUTE, NOW() - INTERVAL 2 MINUTE),
(3, 'P012', 2, 1, 15, 'preferencial', 'chamada', 3, NOW() - INTERVAL 8 MINUTE, NOW() - INTERVAL 1 MINUTE),
(4, 'S011', 3, 2, 5, 'servico', 'aguardando', NULL, NOW() - INTERVAL 3 MINUTE, NULL),
(5, 'H018', 4, 3, 8, '80+', 'aguardando', NULL, NOW() - INTERVAL 4 MINUTE, NULL);

INSERT INTO atendimentos (id, senha_id, atendente_id, inicio, fim, observacoes) VALUES
(1, 1, 3, NOW() - INTERVAL 80 MINUTE, NOW() - INTERVAL 70 MINUTE, 'Orientação rápida.'),
(2, 2, 4, NOW() - INTERVAL 5 MINUTE, NULL, NULL),
(3, 3, 3, NOW() - INTERVAL 1 MINUTE, NULL, NULL);

INSERT INTO logs_eventos (tipo, referencia_id, payload, criado_em) VALUES
('senha_emitida', 4, JSON_OBJECT('codigo','S011','fila_id',3,'prioridade','servico'), NOW() - INTERVAL 3 MINUTE),
('senha_emitida', 5, JSON_OBJECT('codigo','H018','fila_id',4,'prioridade','80+'), NOW() - INTERVAL 4 MINUTE),
('senha_chamada', 3, JSON_OBJECT('codigo','P012','guiche',3,'prioridade',15,'tipo','preferencial'), NOW() - INTERVAL 1 MINUTE),
('senha_finalizada', 1, JSON_OBJECT('codigo','A001','duracao',600,'guiche',1), NOW() - INTERVAL 70 MINUTE);
