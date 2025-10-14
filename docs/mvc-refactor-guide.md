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
