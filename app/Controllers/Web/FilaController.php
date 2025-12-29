<?php

declare(strict_types=1);

namespace App\Controllers\Web;

use App\Controllers\BaseController;

class FilaController extends BaseController
{
    public function index(): \App\Core\Response
    {
        return $this->view('fila/index', [
            'title' => 'Fila e Tipos de Senha',
        ]);
    }
}
