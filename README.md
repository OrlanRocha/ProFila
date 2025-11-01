# ProFila — Sistema de Senhas & Atendimento (MVC PHP)

ProFila é uma reimplementação moderna, responsiva e compatível com hospedagem compartilhada de um sistema de gerenciamento de filas, painéis de senhas e dashboards operacionais construída em PHP 8 seguindo uma arquitetura MVC enxuta.

## Visão geral da arquitetura

```
/
├── index.php                 # Front controller (raiz do projeto)
├── .htaccess                 # Reescrita opcional para URLs amigáveis (/controller/acao)
├── bootstrap.php             # Autoloader PSR-4 simples
├── app/
│   ├── Core/                 # Núcleo: Router, Controller, View, DB, Session, Csrf, UrlGenerator
│   ├── Controllers/          # Fluxo do app (Auth, Usuários, Filas, Guichês, Senhas, Painel, Permissões, Relatórios, API)
│   ├── Models/               # Acesso a dados (PDO + prepared statements)
│   ├── Services/             # Regras de negócio (autenticação, filas, relatórios, SSE/painéis)
│   └── Views/                # Layout e telas (Bootstrap 5, DataTables, Chart.js, SweetAlert2)
├── config/env.php            # Configurações (db, base_url, timezone, SSE, URLs amigáveis)
├── public/assets/            # CSS, JS e scripts do painel
├── sse/stream.php            # Endpoint Server-Sent Events para o painel público
└── sql/
    ├── schema.sql            # DDL + seeds iniciais
    └── seeds.sql             # Conjunto de dados ampliado para cenários de teste
```

## Principais recursos

- **Rotas amigáveis por padrão** (`.htaccess` incluso, com fallback para `?r=controller/acao`).
- **Paineis multiunidade**: cada display registra automaticamente seu IP, aguarda atribuição de órgão/cliente/regra e recebe chamadas filtradas por unidade.
- **Gestão granular de permissões**: matriz de perfis × funcionalidades com persistência em banco (`permissoes.manage`).
- **Guichês inteligentes**: configuração de modo de chamada (FIFO/Sequencial), prioridades atendidas (padrão, preferencial, 80+, serviço) e vínculo com unidades.
- **Senhas com múltiplas categorias**: emissão e tratamento respeitando pesos e regras dos guichês.
- **Dashboard e relatórios** com Chart.js, indicadores diários e exportações via DataTables.
- **Painel público** com SSE (fallback em polling), som sintetizado via Web Audio API e identificação de unidade atribuída.

## Pré-requisitos

- PHP 8.1+ com extensões `pdo_mysql` e `json` habilitadas.
- Servidor MySQL 8+ ou MariaDB compatível.
- Servidor web que aponte para `index.php` na raiz. Para URLs amigáveis habilite o `mod_rewrite` ou ajuste o `.htaccess` conforme o subdiretório.

## Instalação

1. **Enviar os arquivos** mantendo a estrutura acima para a hospedagem compartilhada.
2. **Configurar o banco de dados**:
   - Crie um schema vazio (ex.: `profila`).
   - Importe `sql/schema.sql` para gerar tabelas, permissões e seeds padrão.
   - Opcional: carregue `sql/seeds.sql` para um cenário de testes mais amplo.
3. **Atualizar `config/env.php`**:
   - Informar host, nome, usuário e senha do banco em `db`.
   - Ajustar `base_url` se o projeto estiver em uma subpasta (ex.: `/profila`).
   - Definir `pretty_urls` (`true` por padrão) e parâmetros de SSE/polling conforme recursos do provedor.
4. **Garantir permissões de escrita** em `storage/cache/` (cache para SSE e logs rápidos).

## Primeiro acesso

- Login: acesse `https://seusite.com/auth/login` (ou `index.php?r=auth/login` caso o servidor não aplique o `.htaccess`).
- Credenciais iniciais: `admin@local` / `admin123` (altere imediatamente).
- Após o login, os módulos exibidos respeitam as permissões aplicadas ao papel do usuário.

## Módulos principais

- **Dashboard & Relatórios** (`dashboard.view`, `relatorios.view`): indicadores diários, gráficos de volume por fila/dia e taxa de priorização.
- **Usuários** (`usuarios.manage`): CRUD completo com reset de senha e ativação/desativação.
- **Permissões** (`permissoes.manage`): matriz para liberar ou revogar funcionalidades por perfil.
- **Filas** (`filas.manage`): vínculo por unidade, sigla, prioridade base e status.
- **Guichês** (`guiches.manage`): configuração de modo, prioridades aceitas, unidade padrão e fila default.
- **Senhas** (`senhas.emit` / `senhas.operate`): emissão (padrão, preferencial, 80+, serviço), chamada/rechamada, finalização e transferência.
- **Painéis** (`painel.manage`): lista de displays detectados por IP, atribuição de órgão/cliente/unidade/regra e cadastro de novas regras.
- **Painel público** (`painel.view`): tela fullscreen, histórico e som sintetizado. Ao carregar, registra IP e aguarda atribuição.

## Painel em tempo real

- `sse/stream.php?display=<token>` transmite eventos `event: senha` com `{ codigo, guiche, fila, hora, unidade_id }`.
- Displays recém-detectados permanecem em status `pendente` até que uma unidade/regra seja atribuída na central de gestão.
- Fallback automático via Fetch API (`api/painel/last?display=<token>`) com intervalo configurável (`poll_interval_ms`).

## Segurança

- Sessões com regeneração pós-login e rate limiting simples.
- Hash de senha (`password_hash`/`password_verify`).
- Tokens CSRF nos POST críticos.
- Prepared statements (PDO) em todas as interações com o banco.
- Saída sanitizada (`htmlspecialchars`) nas views.

## Customização

- **Tema**: `public/assets/css/app.css` (cores, gradientes, botões).
- **Scripts globais**: `public/assets/js/app.js` (toasts, DataTables, gráficos).
- **Painel público**: `public/assets/js/painel.js` (SSE, fallback, áudio).
- **Regras adicionais para displays**: cadastre novas configurações em “Painéis > Contexto institucional”.

## Scripts úteis

Validação de sintaxe PHP:

```bash
find app -name "*.php" -print0 | xargs -0 -n1 php -l
php -l index.php sse/stream.php
```

## Credenciais iniciais

| Usuário     | Senha    | Papel |
|-------------|----------|-------|
| admin@local | admin123 | admin |

---

Feito para ambientes de hospedagem compartilhada: sem frameworks full-stack, apenas PHP puro com Bootstrap 5, DataTables, SweetAlert2, Toastify e Chart.js.
