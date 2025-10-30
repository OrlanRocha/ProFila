<?php
declare(strict_types=1);

namespace App\Core;

final class UrlGenerator
{
    public static function make(string $route, array $config, array $params = []): string
    {
        $usePretty = (bool) ($config['app']['pretty_urls'] ?? true);
        $basePath = self::basePath($config);

        $route = trim($route, '/');
        if ($route === '') {
            return $basePath !== '' ? $basePath . '/' : '/';
        }

        if ($usePretty) {
            [$controller, $action] = array_pad(explode('/', $route), 2, 'index');
            $path = $controller;
            if ($action !== 'index') {
                $path .= '/' . $action;
            }
            $url = rtrim($basePath ?: '', '/') . '/' . ltrim($path, '/');
            if (!empty($params)) {
                $url .= '?' . http_build_query($params);
            }
            return $url === '' ? '/' : $url;
        }

        $query = ['r' => $route];
        if (!empty($params)) {
            $query = array_merge($query, $params);
        }

        $prefix = rtrim($basePath ?: '', '/');
        $prefix = $prefix === '' ? '' : $prefix;
        $url = $prefix . '/index.php?' . http_build_query($query);
        return $url;
    }

    public static function basePath(array $config): string
    {
        $configured = $config['app']['base_url'] ?? '';
        if (is_string($configured) && $configured !== '') {
            if (filter_var($configured, FILTER_VALIDATE_URL)) {
                $path = parse_url($configured, PHP_URL_PATH) ?? '';
                return rtrim($path, '/');
            }

            return rtrim($configured, '/');
        }

        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $dir = rtrim(str_replace('index.php', '', $scriptName), '/');
        if ($dir === '' || $dir === '.') {
            return '';
        }

        return $dir;
    }
}
