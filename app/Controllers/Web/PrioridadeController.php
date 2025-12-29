<?php

declare(strict_types=1);

namespace App\Controllers\Web;

use App\Controllers\BaseController;

class PrioridadeController extends BaseController
{
    public function index(): \App\Core\Response
    {
        return $this->view('prioridade/index', [
            'title' => 'Prioridades',
        ]);
    }
}
