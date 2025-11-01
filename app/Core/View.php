<?php
declare(strict_types=1);

namespace App\Core;

class View
{
    public static function render(string $template, array $data = []): string
    {
        $content = self::renderPartial($template, $data);
        return self::renderPartial('layouts/main', array_merge($data, ['content' => $content]));
    }

    public static function renderStatic(string $template, array $data = []): string
    {
        return self::renderPartial($template, $data);
    }

    private static function renderPartial(string $template, array $data): string
    {
        $file = __DIR__ . '/../Views/' . $template . '.php';
        if (!is_file($file)) {
            throw new HttpNotFoundException('View não encontrada: ' . $template);
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $file;
        return (string) ob_get_clean();
    }
}
