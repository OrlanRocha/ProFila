<?php

declare(strict_types=1);

namespace App\Controllers\Web;

use App\Controllers\BaseController;

class MonitoramentoController extends BaseController
{
    public function index(): \App\Core\Response
    {
        return $this->view('monitoramento/index', [
            'title' => 'Monitoramento',
        ]);
    }
}
