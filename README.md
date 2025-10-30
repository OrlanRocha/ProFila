# ProFila — Sistema de Senhas & Atendimento (MVC PHP)

ProFila é uma implementação moderna e responsiva de um sistema de gerenciamento de filas, painéis de senhas e dashboards operacionais construída em PHP 8 utilizando uma arquitetura MVC enxuta.

## Visão geral da arquitetura

```
/
├── index.php                 # Front controller (raiz do projeto)
├── bootstrap.php             # Autoloader PSR-4 simples
├── app/
│   ├── Core/                 # Núcleo: Router, Controller, View, DB, Session, Csrf
│   ├── Controllers/          # Regras de fluxo (Auth, Usuários, Filas, Guichês, Senhas, Painel, Relatórios, API)
│   ├── Models/               # Camada de acesso a dados (PDO + prepared statements)
│   ├── Services/             # Regras de negócio (autenticação, filas, relatórios, SSE)
│   └── Views/                # Layouts e páginas (Bootstrap 5, DataTables, Chart.js)
├── config/env.php            # Configurações de app e banco para hospedagem compartilhada
├── public/assets/            # CSS, JS e áudio do painel
├── sse/stream.php            # Endpoint Server-Sent Events para o painel público
└── sql/schema.sql            # DDL + seeds iniciais
```

## Pré-requisitos

- PHP 8.1+ com extensões `pdo_mysql` e `json` habilitadas.
- Servidor MySQL 8+ (ou MariaDB compatível).
- Servidor web ou CLI com suporte a PHP para servir `index.php` na raiz (não é necessário mod_rewrite, rotas usam `?r=controller/acao`).

## Instalação

1. **Clonar o repositório** ou enviar os arquivos para a hospedagem compartilhada mantendo a estrutura de diretórios.
2. **Configurar o banco de dados:**
   - Crie um database vazio (ex.: `profila`).
   - Importe `sql/schema.sql` para gerar as tabelas e seeds iniciais (usuário admin `admin@local` com senha `admin123`).
3. **Atualizar configurações:**
   - Edite `config/env.php` informando host, nome, usuário e senha do banco.
   - Ajuste `base_url` caso o sistema rode em uma subpasta (ex.: `/profila`).
   - Configure `sse_enabled` e `poll_interval_ms` conforme recursos do provedor.
4. **Permissões de escrita:** garanta que `storage/cache/` possa gravar arquivos (necessário para cache do painel/SSE).

## Uso

- Acesse `https://seusite.com/index.php?r=auth/login` e autentique-se com `admin@local` / `admin123`.
- Navegação principal:
  - **Dashboard / Relatórios:** indicadores, gráficos (Chart.js) e taxa de priorização.
  - **Usuários, Filas, Guichês:** CRUD completo com DataTables (paginação, busca, exportação CSV/Print) e SweetAlert2 para confirmações.
  - **Senhas:** emissão (normal/prioritária), operação do atendente (chamar próxima, rechamar, finalizar, transferir).
  - **Painel Público:** tela fullscreen com SSE (fallback em polling via Fetch API) e áudio de chamada.

## Painel em tempo real

- `sse/stream.php` transmite eventos `event: senha` com JSON `{ codigo, guiche, fila, hora }`.
- Caso a hospedagem encerre conexões longas, o painel usa polling configurável (`poll_interval_ms`) em `?r=api/painel/last`.
- O áudio de chamada é reproduzido com um tom sintético via Web Audio API (sem necessidade de arquivos adicionais).

## Segurança

- Sessões iniciadas no front controller (`index.php`).
- Hash de senha via `password_hash` / `password_verify`.
- Rate limiting simples na tela de login.
- Tokens CSRF em POST críticos (CRUD, emissão/operacão de senhas).
- Saída sanitizada nas views com `htmlspecialchars`.
- Prepared statements (PDO) em toda interação com o banco.

## Customização rápida

- **Temas/estilos:** ajuste `public/assets/css/app.css`.
- **Notificações/toasts:** centralizadas em `public/assets/js/app.js`.
- **Sons e alertas:** o painel utiliza um tom gerado via Web Audio API (sem arquivos binários).
- **Fallback de painel:** altere `config/env.php` para desativar SSE ou aumentar/diminuir o intervalo de polling.

## Scripts úteis

Validação rápida de sintaxe PHP:

```bash
find app -name "*.php" -print0 | xargs -0 -n1 php -l
php -l index.php sse/stream.php
```

## Credenciais iniciais

| Usuário      | Senha    | Papel  |
|--------------|----------|--------|
| admin@local  | admin123 | admin  |

Altere a senha imediatamente após o primeiro login.

---

Feito com ❤️ para ambientes de hospedagem compartilhada: sem dependência de frameworks full-stack, apenas PHP puro, Bootstrap 5, DataTables, SweetAlert2, Toastify e Chart.js.
