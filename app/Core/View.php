<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    public static function render(string $template, array $data = [], string $layout = 'app'): string
    {
        $content = self::renderPartial($template, $data);
        $layoutFile = __DIR__ . '/../Views/layouts/' . $layout . '.php';

        if (!is_file($layoutFile)) {
            return $content;
        }

        ob_start();
        extract($data);
        $yield = $content;
        require $layoutFile;

        return (string) ob_get_clean();
    }

    public static function renderPartial(string $template, array $data = []): string
    {
        $file = __DIR__ . '/../Views/' . $template . '.php';

        if (!is_file($file)) {
            return '<p>View não encontrada: ' . htmlspecialchars($template) . '</p>';
        }

        ob_start();
        extract($data);
        require $file;

        return (string) ob_get_clean();
    }
}
