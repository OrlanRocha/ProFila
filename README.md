# ProFila — Painel de Senhas (MVC)

A versão refatorada do ProFila aplica o guia de migração para o padrão **Model-View-Controller (MVC)** em PHP. O código foi reorganizado em camadas claras, com roteamento centralizado, controllers finos, services com regras de negócio e views desacopladas da persistência.

## Visão geral

- **Front controller único** em `public/index.php` inicializa autoload, sessão e delega o fluxo para `Core\App`.
- **Router minimalista** (`core/Router.php`) registra rotas GET/POST e mapeia para controllers.
- **Camada de serviços** concentra regras de autenticação e gestão da fila de atendimentos.
- **Views responsivas** em `app/Views` usam um layout base e componentes reutilizáveis.
- **Configuração via `.env`** para isolar credenciais sensíveis e parâmetros de execução.

## Estrutura de pastas

```
app/
├── Controllers/
│   ├── AtendimentoController.php
│   ├── AuthController.php
│   └── DashboardController.php
├── Models/
│   ├── Counter.php
│   ├── Sector.php
│   ├── Ticket.php
│   └── User.php
├── Services/
│   ├── AuthService.php
│   ├── DashboardService.php
│   └── TicketService.php
└── Views/
    ├── auth/
    ├── dashboard/
    ├── layouts/
    └── tickets/
config/
└── database.php
core/
├── App.php
├── Controller.php
├── Env.php
├── Router.php
├── Session.php
└── View.php
public/
├── assets/
│   ├── css/app.css
│   └── js/app.js
└── index.php
database/
└── schema.sql
```

## Pré-requisitos

- PHP 8.1+
- Composer (opcional, caso deseje adicionar dependências externas)
- Servidor MySQL 8+ ou MariaDB 10.4+

## Configuração

1. Copie o arquivo `.env` (ou ajuste os valores existentes) com as credenciais do banco:

   ```env
   APP_NAME=ProFila
   APP_ENV=local
   APP_DEBUG=true
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_NAME=profila
   DB_USER=root
   DB_PASS=secret
   ```

2. Crie o banco e os dados de referência executando o script `database/schema.sql` no MySQL:

   ```bash
   mysql -u root -p < database/schema.sql
   ```

   O script cria as tabelas `users`, `sectors`, `counters` e `tickets`, além de inserir um usuário administrador (`admin@profila.local` / `admin123`).

3. Suba um servidor PHP apontando para a pasta `public/` (ex.: `php -S localhost:8000 -t public`).

4. Acesse `http://localhost:8000` e entre com as credenciais do administrador.

## Funcionalidades principais

- **Autenticação** com hashing seguro (`password_hash`) e atualização automática de hash legado.
- **Dashboard** com contagem de senhas em diferentes estados e histórico recente.
- **Gestão de atendimentos**:
  - geração de novas senhas por setor e prioridade;
  - chamada da próxima senha com distribuição por guichê;
  - finalização manual do atendimento.
- **API interna** (`/api/counters`) para carregamento dinâmico de guichês por setor.

## Banco de dados refatorado

As principais entidades foram normalizadas conforme as responsabilidades do domínio:

| Tabela    | Responsabilidade                                                     |
|-----------|---------------------------------------------------------------------|
| `users`   | Usuários do sistema (administrador, atendentes, consultores).       |
| `sectors` | Agrupa atendimentos por área/serviço e define o prefixo da senha.   |
| `counters`| Guichês vinculados a um setor, utilizados na chamada das senhas.    |
| `tickets` | Registro das senhas com prioridade, estado e timestamps de ciclo.   |

Os services `TicketService` e `DashboardService` encapsulam as operações SQL com `PDO`, garantindo transações e consultas otimizadas.

## Scripts úteis

| Comando                                   | Descrição                                      |
|-------------------------------------------|------------------------------------------------|
| `php -S localhost:8000 -t public`         | Inicia o servidor de desenvolvimento embutido. |
| `mysql -u root -p < database/schema.sql`  | Provisiona o banco com estrutura + seed.       |

## Próximos passos sugeridos

- Adicionar testes de unidade com PHPUnit para services e controllers.
- Integrar `vlucas/phpdotenv` para carregamento robusto do `.env` em produção.
- Criar módulo de relatórios utilizando `App\Services\ReportService` (conforme guia original).

---

> Consulte `docs/mvc-refactor-guide.md` para detalhes conceituais adicionais sobre a migração para MVC e boas práticas de UI/UX.
