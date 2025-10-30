<?php

declare(strict_types=1);

namespace App\Models;

final class Ticket
{
    public const STATUS_WAITING = 'waiting';
    public const STATUS_CALLED = 'called';
    public const STATUS_FINISHED = 'finished';

    public const PRIORIDADE_PADRAO = 'normal';

    /** @var string[] */
    public const PRIORITIES = ['emergency', 'priority', 'normal'];
}
