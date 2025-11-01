<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Models\Permissao;

class PermissaoController extends Controller
{
    private Permissao $permissoes;
    private const PAPEIS = ['admin', 'gestor', 'atendente', 'visor'];

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->permissoes = new Permissao($config);
    }

    public function index(): void
    {
        $this->requirePermission('permissoes.manage');
        $matriz = [];
        foreach (self::PAPEIS as $papel) {
            $matriz[$papel] = $this->permissoes->porPapel($papel);
        }

        $this->view('permissoes/index', [
            'permissoes' => $matriz,
            'token' => Csrf::token($this->session),
        ]);
    }

    public function salvar(): void
    {
        $this->requirePermission('permissoes.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validate($this->session, $_POST['_token'] ?? null)) {
            throw new \InvalidArgumentException('Requisição inválida.');
        }
        $dados = $_POST['permissoes'] ?? [];
        $catalogo = $this->permissoes->todas();
        foreach (self::PAPEIS as $papel) {
            if (!in_array($papel, self::PAPEIS, true)) {
                continue;
            }
            $normalizado = [];
            foreach ($catalogo as $perm) {
                $permissaoId = (int) $perm['id'];
                $valor = $dados[$papel][$permissaoId] ?? '0';
                $normalizado[$permissaoId] = $valor === '1' ? 1 : 0;
            }
            $this->permissoes->atualizarPapel($papel, $normalizado);
        }
        $this->session->set('flash', 'Permissões atualizadas com sucesso.');
        $this->redirect('permissoes/index');
    }
}
