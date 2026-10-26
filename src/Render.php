<?php
declare(strict_types=1);

namespace Hire;

final class Render
{
    public static function e(mixed $v): string
    {
        return htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function page(array $record): string
    {
        ob_start();
        $r = $record;
        require __DIR__ . '/../templates/service.php';
        return (string) ob_get_clean();
    }

    public static function index(array $records, string $title): string
    {
        ob_start();
        require __DIR__ . '/../templates/index.php';
        return (string) ob_get_clean();
    }

    public static function notFound(): string
    {
        return self::layout('Not found', '<main><h1>Page not found</h1></main>');
    }

    public static function layout(string $title, string $body): string
    {
        ob_start();
        require __DIR__ . '/../templates/layout.php';
        return (string) ob_get_clean();
    }
}
