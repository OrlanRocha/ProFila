# ProFila — Guia de Schema, RBAC e Telas (MVP)

Este documento consolida o schema SQL inicial sugerido, a matriz de permissões por perfil, orientações de telas-chave e fluxo de integração API → WebSocket via outbox.

> **Compatibilidade**: MySQL 8+ ou MariaDB 10.4+. Se JSON não estiver disponível, troque campos `JSON` por `LONGTEXT`.

---

## 1) Schema SQL (núcleo)

O SQL abaixo cobre UOs, RBAC, filas, pontos, tickets, monitores e outbox WS.

```
-- SCHEMA RESUMIDO (trecho; use database/schema.sql como referência para evoluir)
-- UO I..IV
CREATE TABLE uoi (...);
CREATE TABLE uoii (... fk uoi);
CREATE TABLE uoiii (... fk uoi,uoii);
CREATE TABLE uoiv (... fk uoi,uoii,uoiii);

-- Segurança
CREATE TABLE roles (...);
CREATE TABLE permissions (...);
CREATE TABLE role_permissions (... fk roles,permissions);
CREATE TABLE users (... fk roles);
CREATE TABLE user_scopes (... fk users, uo*);
CREATE TABLE auth_attempts (...);
CREATE TABLE auth_logs (... fk users);
CREATE TABLE audit_logs (... fk users);

-- Serviços
CREATE TABLE service_groups (...);
CREATE TABLE services (... fk service_groups);

-- Filas e políticas
CREATE TABLE queues (... inclui UO, política, SLA, flags);
CREATE TABLE queue_ticket_types (... letra/faixa/peso por fila);
CREATE TABLE queue_policies (... policy_type + config_json);

-- Layout / canais / monitores
CREATE TABLE layouts (... cores, flags);
CREATE TABLE channels (... fk UO, linhas, intervalo);
CREATE TABLE monitors (... fk channel/layout);

-- Pontos
CREATE TABLE points_reception (... fk UO);
CREATE TABLE points_service (... fk UO, policy_mode opcional);

-- Operação
CREATE TABLE tickets (... fk queue, ticket_type, service, point; locks);
CREATE TABLE ticket_events (... fk ticket, user, point);

-- Outbox WS
CREATE TABLE ws_outbox (... room, msg_type, payload_json, tries, sent_at);
```

Para ver a versão detalhada, consulte o anexo no corpo da issue ou adapte diretamente em `database/schema.sql` conforme necessidade do ambiente.

---

## 2) Matriz RBAC (perfis x permissões)

| Permissão                         | Atendente | Supervisor | Gestor | Admin Master | Auditor |
| --------------------------------- | :-------: | :--------: | :----: | :----------: | :-----: |
| ticket.create                     |           |            |   ✅    |      ✅       |         |
| ticket.next / call / recall / ... |    ✅     |     ✅     |        |      ✅       |         |
| ticket.cancel / transfer / ...    |           |     ✅     |        |      ✅       |         |
| report.view                       |           |     ✅     |   ✅    |      ✅       |   ✅    |
| queue.manage / policy.manage      |           |            |   ✅    |      ✅       |         |
| monitor.manage / point.manage     |           |            |   ✅    |      ✅       |         |
| user.view                         |           |            |  ✅*   |      ✅       |   ✅    |
| user.manage                       |           |            |        |      ✅       |         |
| audit.view                        |           |            |   ✅    |      ✅       |   ✅    |
| system.params                     |           |            |        |      ✅       |         |

> *`user.view` para Gestor é opcional conforme política local. Sempre combine permissões com `user_scopes`.

---

## 3) Telas essenciais (UX)

### Login (`/login`)
- E-mail e senha; Toastr para erros; redireciona para dashboard/atendimento.
- Se `must_change_password=1`, levar para `/minha-conta/trocar-senha`.

### Usuários (`/usuarios`)
- DataTables com: ativo, nome, email, CPF, perfil, escopos.
- Ações: editar, ativar/inativar (SweetAlert2 com motivo), reset de senha (motivo + gerar ou informar), logs, escopos.

### Atendimento (`/atendimento`)
- Seleciona fila + ponto; ações: próxima, chamar, iniciar, finalizar, rechamar, cancelar/transferir/reinserir (supervisor).
- Painel de “Senha Atual” + últimas chamadas; eventos WS atualizam UI.

### Monitor (`/monitor/{channelId}`)
- Ao abrir: registra monitor via `/api/monitors/register`, conecta no WS (`room=channel:{channelId}`) e exibe últimas chamadas, cabeçalho e informativo.

---

## 4) Integração API → WS (Outbox)

Fluxo recomendado:
1. Em qualquer mudança de ticket: transação DB
2. Inserir em `ticket_events`
3. Inserir em `ws_outbox` (room e payload)
4. Worker/loop do WS lê `sent_at IS NULL`, envia e marca `sent_at=NOW()`. Em falha, `tries++`.

Rooms sugeridos:
- `channel:{channelId}` (painel)
- `queue:{queueId}` (tela interna)
- `uoiv:{uoivId}` (sala de situação)

Formato de mensagem:
```json
{
  "type": "TICKET_CALLED",
  "ts": "2025-12-27T12:34:56-03:00",
  "payload": {
    "ticket_id": 123,
    "display": "A-045",
    "queue_id": 10,
    "point_number": 3,
    "line": 1
  }
}
```

---

## 5) Telas que não podem faltar (governança)
- `/usuarios/{id}/escopos`: atribuir UO(s).
- `/usuarios/{id}/logs`: auditoria do usuário.
- `/auditoria`: listar `audit_logs`.
- `/minha-conta/trocar-senha`: quando `must_change_password=1`.

