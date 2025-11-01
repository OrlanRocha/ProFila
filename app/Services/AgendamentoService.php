<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\DB;
use App\Models\Agendamento;
use App\Models\Servico;
use App\Models\Unidade;

class AgendamentoService
{
    private Agendamento $agendamentos;
    private Servico $servicos;
    private Unidade $unidades;

    public function __construct(private array $config)
    {
        DB::connection($config);
        $this->agendamentos = new Agendamento($config);
        $this->servicos = new Servico($config);
        $this->unidades = new Unidade($config);
    }

    public function listar(): array
    {
        return $this->agendamentos->all();
    }

    public function servicosAtivos(): array
    {
        return $this->servicos->ativos();
    }

    public function unidades(): array
    {
        return $this->unidades->all();
    }

    public function criar(array $dados): int
    {
        return $this->agendamentos->create($dados);
    }
}
