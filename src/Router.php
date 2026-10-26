<?php
declare(strict_types=1);

namespace Hire;

final class Router
{
    /** @return array{status:int, record:?array} */
    public static function resolve(string $path, array $records, string $track = ''): array
    {
        $path = parse_url($path, PHP_URL_PATH) ?: '/';
        if ($path === '/') {
            return ['status' => 200, 'record' => null];
        }
        if (!preg_match('#^/([a-z0-9-]+)/?$#', $path, $m)) {
            return ['status' => 404, 'record' => null];
        }
        foreach ($records as $r) {
            if (($r['slug'] ?? '') === $m[1] && ($track === '' || ($r['track'] ?? '') === $track)) {
                return ['status' => 200, 'record' => $r];
            }
        }
        return ['status' => 404, 'record' => null];
    }
}
