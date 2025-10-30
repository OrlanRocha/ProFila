<?php

declare(strict_types=1);

namespace Core;

use RuntimeException;

final class View
{
    /**
     * @param array<string, mixed> $data
     */
    public static function render(string $template, array $data = []): void
    {
        $viewPath = dirname(__DIR__) . '/app/Views/' . $template . '.php';

        if (! is_file($viewPath)) {
            throw new RuntimeException(sprintf('View %s não encontrada', $template));
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        $layout = $data['layout'] ?? 'layouts/main';
        $layoutPath = dirname(__DIR__) . '/app/Views/' . $layout . '.php';

        if (! is_file($layoutPath)) {
            throw new RuntimeException(sprintf('Layout %s não encontrado', $layout));
        }

        require $layoutPath;
    }
}
