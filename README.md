# Guia de Refatoração para o Padrão MVC em PHP

Este guia apresenta um roteiro para migrar um projeto PHP legado para uma arquitetura **Model-View-Controller (MVC)** clara e sustentável. As etapas a seguir ajudam a separar responsabilidades, organizar diretórios, centralizar o roteamento e exemplificar a implementação com um módulo de gerenciamento de usuários.

---

## 1. Estrutura de Pastas Sugerida

Organize a aplicação em pastas distintas para cada camada da arquitetura. Uma estrutura inicial recomendada é:

```
project-root/
├── app/
│   ├── Controllers/
│   │   ├── BaseController.php
│   │   └── UserController.php
│   ├── Models/
│   │   └── UserModel.php
│   └── Views/
│       ├── layouts/
│       │   └── main.php
│       └── users/
│           ├── userListView.php
│           └── userDetailView.php
├── config/
│   └── database.php
├── core/
│   ├── App.php
│   ├── Controller.php
│   ├── Model.php
│   └── Router.php
├── public/
│   ├── index.php
│   └── assets/
│       ├── css/
│       ├── js/
│       └── images/
├── storage/
│   └── logs/
└── vendor/
    └── autoload.php
```

> Ajuste os diretórios conforme o tamanho do projeto. Por exemplo, a pasta `storage/` pode receber uploads e a pasta `config/` pode centralizar variáveis de ambiente.

---

## 2. Front Controller e Roteador Centralizados

### 2.1 `public/index.php`

Este arquivo é o ponto de entrada único da aplicação. Ele carrega o autoloader, registra as configurações e delega a lógica de roteamento.

```php
<?php
// public/index.php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../core/Router.php';
require __DIR__ . '/../core/App.php';

use Core\App;

session_start();

$app = new App();
$app->run();
```

### 2.2 `core/Router.php`

Um roteador simples interpreta a URL e mapeia para o controller e a ação correspondentes. Para projetos maiores, considere bibliotecas de roteamento, mas para refatorações iniciais um roteador leve costuma ser suficiente.

```php
<?php
// core/Router.php

declare(strict_types=1);

namespace Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function dispatch(string $method, string $uri)
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $handler = $this->routes[$method][$path] ?? null;

        if (! $handler) {
            http_response_code(404);
            echo 'Página não encontrada';
            return;
        }

        return call_user_func($handler);
    }
}
```

### 2.3 `core/App.php`

A classe `App` instancia o roteador, registra as rotas e inicia o fluxo da requisição.

```php
<?php
// core/App.php

declare(strict_types=1);

namespace Core;

use App\Controllers\UserController;

class App
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
        $this->registerRoutes();
    }

    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        $this->router->dispatch($method, $uri);
    }

    private function registerRoutes(): void
    {
        $this->router->get('/', function () {
            (new UserController())->index();
        });

        $this->router->get('/users', function () {
            (new UserController())->index();
        });

        $this->router->get('/users/show', function () {
            $id = isset($_GET['id']) ? (int) $_GET['id'] : null;
            (new UserController())->show($id);
        });
    }
}
```

---

## 3. Boas Práticas de UI/UX para as Views

Uma refatoração arquitetural é ótima oportunidade para revisar a experiência do usuário. Considere os pontos a seguir ao criar novas Views ou adaptar as existentes:

- **Layouts consistentes:** utilize um layout base (`app/Views/layouts/main.php`) para padronizar cabeçalho, rodapé e barra de navegação. Isso reduz duplicidade e mantém a navegação previsível.
- **Componentização:** quebre elementos repetidos (cards, tabelas, formulários) em parciais reutilizáveis. Isso facilita manter interações e estilos consistentes.
- **Tipografia e cores acessíveis:** garanta contraste adequado (WCAG AA), tamanhos de fonte legíveis e destaque visual para ações primárias.
- **Feedback instantâneo:** informe o status das ações com mensagens de sucesso/erro, skeletons ou spinners, especialmente após envios de formulários.
- **Responsividade:** utilize um grid fluido (Flexbox/CSS Grid) ou um micro framework CSS para garantir que tabelas, formulários e botões funcionem bem em telas menores.
- **Estados vazios amigáveis:** quando não houver registros, apresente uma mensagem clara, CTA relevante ou instruções para criar o primeiro item.
- **Acessibilidade:** adicione atributos ARIA quando necessário, use rótulos associados a inputs e garanta navegação por teclado.
- **Design system incremental:** documente padrões (componentes, espaçamentos, tons de cor) em um guia interno simples para que a equipe evolua a interface de forma coesa.

Essas diretrizes, combinadas com a separação das Views dentro da arquitetura MVC, ajudam a entregar telas mais intuitivas e consistentes para o usuário final.

---

## 4. Exemplo Prático: Módulo de Usuários

### 4.1 Model — `app/Models/UserModel.php`

Os models encapsulam o acesso a dados. Utilize PDO para conexões seguras e parametrizadas.

```php
<?php
declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOException;

class UserModel
{
    private PDO $db;

    public function __construct(PDO $connection)
    {
        $this->db = $connection;
    }

    public function getAllUsers(): array
    {
        $stmt = $this->db->query('SELECT id, name, email FROM users ORDER BY name');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findUserById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, name, email FROM users WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }
}
```

> Centralize a criação da conexão PDO em `config/database.php`, garantindo que o mesmo objeto seja reaproveitado em diferentes models.

### 4.2 Controller — `app/Controllers/UserController.php`

O controller orquestra a requisição: coleta dados, interage com o model e chama a view apropriada.

```php
<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\UserModel;
use Core\View;
use PDO;

class UserController
{
    private UserModel $userModel;

    public function __construct()
    {
        $pdo = require __DIR__ . '/../../config/database.php';
        $this->userModel = new UserModel($pdo);
    }

    public function index(): void
    {
        $users = $this->userModel->getAllUsers();
        View::render('users/userListView', ['users' => $users, 'title' => 'Lista de Usuários']);
    }

    public function show(?int $id): void
    {
        if ($id === null) {
            header('Location: /users');
            return;
        }

        $user = $this->userModel->findUserById($id);

        if (! $user) {
            http_response_code(404);
            View::render('errors/404', ['message' => 'Usuário não encontrado']);
            return;
        }

        View::render('users/userDetailView', ['user' => $user, 'title' => 'Detalhes do Usuário']);
    }
}
```

> A classe `Core\View` pode ser uma helper simples para carregar templates, encapsulando lógica comum (layouts, sanitização, etc.).

### 4.3 Helper de View — `core/View.php`

```php
<?php

declare(strict_types=1);

namespace Core;

class View
{
    public static function render(string $template, array $data = []): void
    {
        extract($data);
        $viewPath = __DIR__ . '/../app/Views/' . $template . '.php';

        if (! file_exists($viewPath)) {
            throw new \RuntimeException("View {$template} não encontrada");
        }

        require __DIR__ . '/../app/Views/layouts/main.php';
    }
}
```

### 4.4 Layout Principal — `app/Views/layouts/main.php`

```php
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Aplicação MVC') ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <header>
        <h1>Painel Administrativo</h1>
        <nav>
            <a href="/users">Usuários</a>
        </nav>
    </header>
    <main>
        <?php require $viewPath; ?>
    </main>
    <footer>
        <small>&copy; <?= date('Y') ?> Minha Empresa</small>
    </footer>
</body>
</html>
```

### 4.5 View de Lista — `app/Views/users/userListView.php`

```php
<section>
    <h2>Lista de Usuários</h2>

    <?php if (empty($users)): ?>
        <p>Nenhum usuário encontrado.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars((string) $user['id']) ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><a href="/users/show?id=<?= urlencode((string) $user['id']) ?>">Detalhes</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
```

### 4.6 View de Detalhes — `app/Views/users/userDetailView.php`

```php
<section>
    <h2>Detalhes do Usuário</h2>

    <dl>
        <dt>ID</dt>
        <dd><?= htmlspecialchars((string) $user['id']) ?></dd>

        <dt>Nome</dt>
        <dd><?= htmlspecialchars($user['name']) ?></dd>

        <dt>Email</dt>
        <dd><?= htmlspecialchars($user['email']) ?></dd>
    </dl>

    <p><a href="/users">&larr; Voltar à lista</a></p>
</section>
```

---

## 5. Configuração da Conexão PDO — `config/database.php`

Crie um arquivo de configuração que retorne uma instância de `PDO` reutilizável, tratando erros e usando variáveis de ambiente para credenciais sensíveis.

```php
<?php

declare(strict_types=1);

use PDO;
use PDOException;

dotenv()->load(); // Utilize vlucas/phpdotenv ou equivalente.

$dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', getenv('DB_HOST'), getenv('DB_NAME'));
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    return new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), $options);
} catch (PDOException $exception) {
    // Registre o erro e exiba uma mensagem amigável em produção
    error_log($exception->getMessage());
    http_response_code(500);
    exit('Erro ao conectar ao banco de dados.');
}
```

---

## 6. Estratégia de Migração

1. **Mapeie o código atual:** identifique onde estão as lógicas de negócio, consultas SQL e blocos de HTML.
2. **Crie a nova estrutura de pastas:** mova arquivos gradativamente, mantendo o projeto funcional em cada passo.
3. **Centralize a conexão PDO:** substitua usos diretos de `mysqli` ou `mysql_*` por uma classe de conexão compartilhada.
4. **Refatore módulo por módulo:** comece pelo gerenciamento de usuários, migrando os scripts antigos para os novos controllers, models e views.
5. **Implemente o roteamento:** substitua requisições diretas a arquivos PHP por rotas amigáveis e controladores dedicados.
6. **Limpe as views:** remova chamadas diretas ao banco ou lógicas complexas, deixando apenas a apresentação.
7. **Automatize testes e deploy:** após estabilizar a nova arquitetura, configure testes unitários e integração contínua para evitar regressões.

---

## 7. Boas Práticas Complementares

- **Autoloader:** utilize o Composer para carregar automaticamente classes com PSR-4.
- **Controllers finos:** delegue regras de negócio a services ou models para evitar controladores inflados.
- **Tratamento de erros:** implemente páginas e logs para erros 404/500.
- **Segurança:** sanitize saídas com `htmlspecialchars`, valide dados de entrada e proteja-se contra CSRF e XSS.
- **Internacionalização:** caso necessário, centralize strings em arquivos de idioma.

Seguindo estas recomendações, o projeto passa a ser mais modular, testável e fácil de manter, alinhado às boas práticas do desenvolvimento PHP moderno.

---

## 8. Sistema de Registro de Atendimento (MVC + Services)

Para o cenário específico de um **Sistema de Registro de Atendimento** com painel de senhas em tempo real, utilize uma estrutura que deixe explícitas as fronteiras entre Models, Services e Controllers, além de recursos de infraestrutura (Core).

### 8.1 Estrutura de pastas recomendada

```
project-root/
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   └── AtendimentoController.php
│   ├── Models/
│   │   ├── Atendimento.php
│   │   └── User.php
│   ├── Services/
│   │   ├── AtendimentoService.php
│   │   ├── AuthService.php
│   │   ├── ReportService.php
│   │   └── SenhaService.php
│   └── Views/
│       ├── auth/
│       │   ├── login.php
│       │   └── register.php
│       ├── painel/
│       │   └── display.php
│       └── atendimentos/
│           ├── form.php
│           └── index.php
├── config/
│   └── database.php
├── core/
│   ├── App.php
│   ├── Router.php
│   ├── View.php
│   └── Session.php
├── public/
│   ├── index.php
│   └── assets/
│       ├── css/
│       ├── js/
│       └── sounds/
└── websocket/
    └── publisher.php
```

> A pasta `websocket/` pode conter scripts auxiliares responsáveis por publicar mensagens para o servidor WebSocket (Ratchet, Swoole, etc.).

### 8.2 Classes essenciais por camada

| Camada | Classe (namespace) | Responsabilidade |
| ------ | ------------------ | ---------------- |
| Model  | `App\Models\User` | Representa usuários (admin, atendente), encapsulando atributos como `id`, `name`, `email`, `passwordHash` e `role`. Fornece métodos de hidratação e validações simples do domínio (ex.: garantir papéis válidos). |
| Model  | `App\Models\Atendimento` | Modela o atendimento associado ao cliente, senha chamada, timestamps de início/fim e atendente responsável. Pode expor helpers para calcular duração e status derivado (`em_andamento`, `finalizado`). |
| Controller | `App\Controllers\AuthController` | Recebe requisições de login/logout e cadastro de usuários, delega a validação ao `AuthService` e injeta os resultados nas Views (`auth/login.php`, `auth/register.php`). |
| Controller | `App\Controllers\AtendimentoController` | Orquestra telas de criação, atualização e finalização de atendimentos, aciona `AtendimentoService`/`SenhaService` e direciona para Views (`atendimentos/index.php`, `painel/display.php`). |
| Service | `App\Services\AuthService` | Centraliza autenticação: busca usuário por e-mail via PDO, verifica senha com `password_verify`, aplica regras de bloqueio e realiza auditoria (último login, IP). Também expõe métodos para cadastro e alteração de papéis. |
| Service | `App\Services\AtendimentoService` | Implementa a lógica de criar, atualizar estado e finalizar atendimentos, mantendo transações com o banco e disparando eventos para o painel quando necessário. |
| Service | `App\Services\SenhaService` | Gera senhas sequenciais, controla a fila e registra qual guichê chamou a senha. Após atualizar o banco, notifica o servidor WebSocket com o próximo atendimento. |
| Service | `App\Services\ReportService` | Consulta atendimentos finalizados com filtros de período, atendente e status. Executa joins com usuários e clientes para entregar datasets prontos para as Views ou exportações (CSV/PDF).

---

## 9. Fluxo de Autenticação — `App\Services\AuthService`

O método `attemptLogin($email, $password)` encapsula a autenticação segura utilizando PDO e `password_verify()`. Ele retorna um array com dados essenciais do usuário autenticado ou `null` quando as credenciais são inválidas.

```php
<?php

declare(strict_types=1);

namespace App\Services;

use PDO;
use PDOException;

class AuthService
{
    public function __construct(private PDO $connection)
    {
    }

    public function attemptLogin(string $email, string $password): ?array
    {
        $sql = 'SELECT id, name, email, password_hash, role FROM users WHERE email = :email LIMIT 1';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':email', mb_strtolower($email));
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (! $user || ! password_verify($password, $user['password_hash'])) {
            return null;
        }

        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $this->rehashPassword((int) $user['id'], $password);
        }

        $this->registerSuccessfulLogin((int) $user['id']);

        unset($user['password_hash']);

        return $user;
    }

    private function rehashPassword(int $userId, string $plainPassword): void
    {
        $hash = password_hash($plainPassword, PASSWORD_DEFAULT);

        $update = $this->connection->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
        $update->bindValue(':hash', $hash);
        $update->bindValue(':id', $userId, PDO::PARAM_INT);
        $update->execute();
    }

    private function registerSuccessfulLogin(int $userId): void
    {
        $update = $this->connection->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id');
        $update->bindValue(':id', $userId, PDO::PARAM_INT);
        $update->execute();
    }
}
```

> Centralize a criação do objeto PDO no container/`config/database.php` e injete-o via construtor para facilitar testes.

---

## 10. Painel de Senhas em Tempo Real

### 10.1 Service — `App\Services\SenhaService`

O método `callNextSenha(int $guicheId)` deve executar três passos atômicos dentro de uma transação:

1. **Selecionar a próxima senha disponível** (status `aguardando`) com `SELECT ... FOR UPDATE` para evitar condições de corrida.
2. **Atualizar o registro** marcando a senha como `chamada`, registrando `guiche_id` e timestamp.
3. **Notificar o servidor WebSocket** publicando um JSON simples (`{"senha":"A001","guiche":3}`) em um canal conhecido.

```php
<?php

declare(strict_types=1);

namespace App\Services;

use PDO;
use PDOException;

interface TicketBroadcaster
{
    public function publish(string $channel, string $payload): void;
}

class SenhaService
{
    private const CHANNEL = 'painel.senhas';

    public function __construct(private PDO $connection, private TicketBroadcaster $broadcaster)
    {
    }

    public function callNextSenha(int $guicheId): ?array
    {
        $this->connection->beginTransaction();

        try {
            $stmt = $this->connection->prepare(
                'SELECT id, codigo, prioridade FROM senhas
                 WHERE status = :status
                 ORDER BY prioridade DESC, criado_em ASC
                 LIMIT 1 FOR UPDATE'
            );
            $stmt->execute([':status' => 'aguardando']);

            $senha = $stmt->fetch(PDO::FETCH_ASSOC);

            if (! $senha) {
                $this->connection->rollBack();
                return null;
            }

            $update = $this->connection->prepare(
                'UPDATE senhas
                 SET status = :novoStatus, chamado_em = NOW(), guiche_id = :guiche
                 WHERE id = :id'
            );
            $update->execute([
                ':novoStatus' => 'chamada',
                ':guiche' => $guicheId,
                ':id' => $senha['id'],
            ]);

            $this->connection->commit();

            $payload = json_encode([
                'senha' => $senha['codigo'],
                'prioridade' => $senha['prioridade'],
                'guiche' => $guicheId,
                'chamado_em' => (new \DateTimeImmutable())->format(DATE_ATOM),
            ], JSON_THROW_ON_ERROR);

            $this->broadcaster->publish(self::CHANNEL, $payload);

            return [
                'codigo' => $senha['codigo'],
                'prioridade' => $senha['prioridade'],
                'guiche' => $guicheId,
            ];
        } catch (PDOException $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }
}
```

> O `TicketBroadcaster` pode ser implementado via chamada a um script Ratchet (`php websocket/publisher.php {payload}`), Redis Pub/Sub ou Swoole task worker, mantendo o service independente da tecnologia de transporte.

### 10.2 View — `app/Views/painel/display.php`

O painel deve conectar-se ao WebSocket e atualizar a interface assim que uma nova mensagem for recebida. Também é possível acionar um alerta sonoro para chamar a atenção do público.

```php
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel de Senhas</title>
    <link rel="stylesheet" href="/assets/css/painel.css">
</head>
<body>
    <main class="painel">
        <h1>Senha Atual</h1>
        <p id="senha-atual" class="senha">Aguardando...</p>
        <p id="guiche-atual" class="guiche">Guichê --</p>
    </main>

    <audio id="alert-sound" src="/assets/sounds/alert.mp3" preload="auto"></audio>

    <script>
        const socket = new WebSocket('wss://example.com:8080/painel');
        const senhaAtual = document.getElementById('senha-atual');
        const guicheAtual = document.getElementById('guiche-atual');
        const alertSound = document.getElementById('alert-sound');

        socket.addEventListener('message', (event) => {
            try {
                const data = JSON.parse(event.data);
                senhaAtual.textContent = data.senha;
                guicheAtual.textContent = `Guichê ${data.guiche}`;

                if (document.visibilityState === 'visible') {
                    alertSound.currentTime = 0;
                    alertSound.play().catch(() => {});
                }
            } catch (error) {
                console.error('Mensagem inválida recebida do WebSocket', error);
            }
        });

        socket.addEventListener('open', () => console.info('Canal de senhas conectado.'));
        socket.addEventListener('close', () => console.warn('Canal de senhas desconectado. Tentando reconectar...'));
    </script>
</body>
</html>
```

---

## 11. Controle de Acesso e Segurança

Após um login bem-sucedido, salve as informações relevantes na sessão:

```php
$_SESSION['user'] = [
    'id'    => $user['id'],
    'name'  => $user['name'],
    'email' => $user['email'],
    'role'  => $user['role'], // 'admin' ou 'atendente'
];
```

Para proteger rotas administrativas, implemente uma função `checkAccess()` que verifique se o usuário autenticado possui o papel exigido. Ela pode ser chamada no início de um controller ou action.

```php
<?php

declare(strict_types=1);

namespace App\Core;

function checkAccess(string ...$allowedRoles): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $user = $_SESSION['user'] ?? null;

    if (! $user) {
        header('Location: /login');
        exit;
    }

    if (! in_array($user['role'], $allowedRoles, true)) {
        http_response_code(403);
        exit('Acesso negado.');
    }
}
```

Uso dentro do `UserController` (após o `namespace` e `use`):

```php
checkAccess('admin');
```

Considere complementar o controle com tokens CSRF em formulários e cabeçalhos `SameSite` para cookies de sessão.

---

## 12. Relatórios com `App\Services\ReportService`

O `ReportService` deve consumir dados dos models `User` e `Atendimento`, combinando-os via joins para gerar relatórios ricos. Um relatório de "Atendimentos Finalizados por Atendente no Último Mês" pode seguir o padrão abaixo:

```php
<?php

declare(strict_types=1);

namespace App\Services;

use PDO;

class ReportService
{
    public function __construct(private PDO $connection)
    {
    }

    public function finalizedByAttendantLastMonth(): array
    {
        $sql = <<<'SQL'
            SELECT
                u.id            AS attendant_id,
                u.name          AS attendant_name,
                COUNT(a.id)     AS total_atendimentos,
                AVG(TIMESTAMPDIFF(MINUTE, a.iniciado_em, a.finalizado_em)) AS tempo_medio_minutos
            FROM atendimentos a
            INNER JOIN users u ON u.id = a.atendente_id
            WHERE a.status = 'finalizado'
              AND a.finalizado_em BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND NOW()
            GROUP BY u.id, u.name
            ORDER BY total_atendimentos DESC
        SQL;

        $stmt = $this->connection->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
```

Os joins garantem que cada atendimento traga o nome do atendente sem múltiplas consultas, reduzindo o custo no banco de dados. Utilize métodos no model `Atendimento` para transformar os resultados (ex.: gerar DTOs) antes de enviá-los às views ou exportadores.
