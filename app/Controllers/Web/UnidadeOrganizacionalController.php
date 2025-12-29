<?php

declare(strict_types=1);

namespace App\Controllers\Web;

use App\Controllers\BaseController;

class UnidadeOrganizacionalController extends BaseController
{
    public function index(): \App\Core\Response
    {
        return $this->view('unidades/index', [
            'title' => 'Unidades Organizacionais',
        ]);
    }

    public function cadastro(): \App\Core\Response
    {
        return $this->view('unidades/cadastro', [
            'title' => 'Cadastro de UO',
        ]);
    }

    public function editar(): \App\Core\Response
    {
        return $this->view('unidades/editar', [
            'title' => 'Editar UO',
        ]);
    }
}
