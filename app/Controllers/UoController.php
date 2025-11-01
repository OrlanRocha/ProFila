<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Models\Uo;

class UoController extends Controller
{
    private Uo $uo;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->uo = new Uo($config);
    }

    public function index(): void
    {
        $this->requirePermission('painel.manage');
        $this->view('uo/index', [
            'uoI' => $this->uo->porNivel('I'),
            'uoII' => $this->uo->porNivel('II'),
            'uoIII' => $this->uo->porNivel('III'),
            'token' => Csrf::token($this->session),
        ]);
    }

    public function salvar(): void
    {
        $this->requirePermission('painel.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validate($this->session, $_POST['_token'] ?? null)) {
            throw new \InvalidArgumentException('Requisição inválida.');
        }

        $nivel = $_POST['nivel'] ?? 'I';
        $nome = trim((string) ($_POST['nome'] ?? ''));
        $codigo = trim((string) ($_POST['codigo'] ?? ''));

        if ($nome === '' || $codigo === '') {
            $this->session->set('flash', 'Informe nome e código para cadastrar a UO.');
            $this->redirect('uo/index');
        }

        $this->uo->create([
            'nivel' => $nivel,
            'nome' => $nome,
            'codigo' => $codigo,
            'ativo' => 1,
        ]);

        $this->session->set('flash', 'UO cadastrada com sucesso.');
        $this->redirect('uo/index');
    }
}
