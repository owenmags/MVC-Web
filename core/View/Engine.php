<?php

declare(strict_types=1);

namespace Core\View;

/**
 * Renders PHP view files with extracted data.
 */
final class Engine
{
    public function __construct(
        private string $viewsPath,
        private string $baseUrl = '',
    ) {
    }

    public function render(string $view, array $data = []): string
    {
        $file = $this->viewsPath . '/' . str_replace('.', '/', $view) . '.php';

        if (!is_file($file)) {
            return "<p>View not found: {$view}</p>";
        }

        $data['base'] = $this->baseUrl;
        extract($data, EXTR_SKIP);

        ob_start();
        include $file;
        return (string) ob_get_clean();
    }
}

