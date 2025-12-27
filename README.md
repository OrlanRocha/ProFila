# ProFila — Sistema de Gestão de Filas (PHP POO + MVC + WebSocket)

ProFila é um esqueleto completo para um sistema de senhas com arquitetura MVC em PHP 8.2, API JSON, atualização em tempo real via WebSocket e layout inicial em Tailwind + DataTables. Ele cobre recepção, atendimento, painel/monitor, dashboards de indicadores e módulos de configuração.

## Visão rápida
- **MVC puro** com Controllers finos, Services ricos e Repositories para persistência (PDO).
- **API JSON** para ações de senha (criar, chamar, iniciar, finalizar, transferir, cancelar) e consultas (filas, monitores, relatórios).
- **WebSocket (Ratchet)** para broadcast de eventos de senha e atualização de painéis.
- **Front-end** em Tailwind (CDN), DataTables, Toastr, SweetAlert2 e módulos JS (`api.js`, `ws.js`, `app.js`).
- **Dominio** estruturado em tickets, eventos e políticas de fila (concorrência e auditoria previstas).
- **Autenticação e cadastro**: fluxos de login/logout e registro de usuários (API + telas).
- **RBAC e escopo**: seeds de roles/permissões e atribuição de perfis aos usuários para montar o menu dinâmico.

## Estrutura de pastas
```
app/
  Controllers/ (Web e Api)
  Core/ (App, Router, Request, Response, View, DB, Auth, Session, Validator, Logger)
  Middlewares/ (Auth, Csrf, Rbac, Scope)
  Models/ (User, Ticket, Queue, etc.)
  Repositories/ (User, Ticket, Queue, Report)
  Services/ (Auth, Ticket, QueuePolicy, Monitor, Report, Audit)
  Views/ (layouts + módulos de tela)
public/
  index.php, .htaccess, assets/js
routes/ (web.php, api.php)
websocket/ (WsHub, WsServer, run.php)
database/schema.sql
bin/ (cli.php, cron.php)
```

## Requisitos
- PHP **8.2+** com extensões `pdo`, `pdo_mysql`, `json`, `mbstring`, `openssl`.
- Composer.
- MySQL/MariaDB.

## Configuração
1. Copie o `.env.example` para `.env` e ajuste credenciais.
2. Instale dependências:
   ```bash
   composer install
   ```
3. Importe o esquema inicial:
   ```bash
   mysql -u root -p profila < database/schema.sql
   ```
4. Inicie o servidor PHP apontando para `public/` ou configure Apache/Nginx com o `.htaccess` incluso.
5. Suba o WebSocket (Ratchet):
   ```bash
   php websocket/run.php
   ```

> Observação: o `TicketRepository` usa armazenamento em memória para facilitar a prototipagem; substitua por consultas PDO conforme o schema.

## Rotas principais
- **Web**: `/login`, `/atendimento`, `/monitor/{channelId}`, `/dashboards`, `/cadastro`, `/config`, `/gerenciamento`.
- **API**: `/api/tickets/*`, `/api/queues`, `/api/monitors/*`, `/api/reports/*`, `/api/users`, `/api/auth/*`.

## Front-end
O layout `app.php` carrega Tailwind, DataTables, Toastr e SweetAlert2 via CDN e os módulos JS locais:
- `public/assets/js/api.js`: wrapper `fetch` para JSON.
- `public/assets/js/ws.js`: cliente WebSocket enxuto.
- `public/assets/js/app.js`: hooks globais (logout etc.).
- Telas de autenticação: `/login` e `/register` usam as APIs `/api/auth/login` e `/api/auth/register`.
- Gestão de usuários: `/usuarios` (protegida), com DataTables, criação e ações críticas com SweetAlert.
- Usuários padrão (teste):
  - Admin: `admin@local` / `secret`
  - Dev/Gestor: `dev@local` / `dev123`

## Próximos passos sugeridos
- Implementar persistência real em todos os repositories utilizando `App\Core\DB` (PDO + transações com lock `locked_by/locked_at`).
- Completar middlewares de RBAC/escopo, acoplando-os às rotas críticas.
- Publicar eventos no `WsHub` ao chamar/iniciar/finalizar senhas.
- Evoluir dashboards (TMA/TME/SLA) e relatórios.
- Adicionar seeds e testes automatizados.
- Executar `php database/seeds/seed.php` para carregar roles/permissões e usuário admin (`admin@local` / `secret`).
