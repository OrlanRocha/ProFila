<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;
use PDO;

abstract class BaseModel
{
    protected PDO $db;

    public function __construct(protected array $config)
    {
        $this->db = DB::connection($config);
    }
}
