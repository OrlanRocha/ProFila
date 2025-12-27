<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$command = $argv[1] ?? null;

echo "Executando rotina: " . ($command ?: 'none') . "\n";
