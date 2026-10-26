<?php
declare(strict_types=1);

namespace Hire;

final class Records
{
    public const TRACKS = ['individuals', 'small-business'];

    /** @return array<int, array> active, valid records; throws on any invalid file */
    public static function load(string $dir): array
    {
        $records = [];
        foreach (glob(rtrim($dir, '/') . '/*.json') ?: [] as $file) {
            $data = json_decode((string) file_get_contents($file), true);
            if (!is_array($data)) {
                throw new \InvalidArgumentException(basename($file) . ': invalid JSON');
            }
            if (($data['status'] ?? '') === 'draft') {
                continue; // drafts may be incomplete; they are never served
            }
            $errors = self::validate($data);
            if ($errors) {
                throw new \InvalidArgumentException(basename($file) . ': ' . implode('; ', $errors));
            }
            if (($data['status'] ?? '') === 'active') {
                $records[] = $data;
            }
        }
        return $records;
    }

    /** @return string[] error messages; empty when valid */
    public static function validate(array $r): array
    {
        $errors = [];
        if (!in_array($r['track'] ?? null, self::TRACKS, true)) {
            $errors[] = 'track must be one of: ' . implode(', ', self::TRACKS);
        }
        if (!isset($r['slug']) || !preg_match('/^[a-z0-9-]+$/', (string) $r['slug'])) {
            $errors[] = 'slug must be lowercase letters, digits and hyphens';
        }
        if (!in_array($r['status'] ?? null, ['active', 'draft'], true)) {
            $errors[] = 'status must be active or draft';
        }
        if (trim((string) ($r['headline'] ?? '')) === '') {
            $errors[] = 'headline is required';
        }
        if (empty($r['steps']) || !is_array($r['steps'])) {
            $errors[] = 'steps is required';
        }
        if (empty($r['packages']) || !is_array($r['packages'])) {
            $errors[] = 'packages is required';
        } else {
            foreach ($r['packages'] as $i => $p) {
                if (trim((string) ($p['name'] ?? '')) === '') {
                    $errors[] = "packages[$i] name is required";
                }
                if (trim((string) ($p['price'] ?? '')) === '') {
                    $errors[] = "packages[$i] price is required";
                }
            }
        }
        $c = $r['contact'] ?? null;
        if (!is_array($c) || trim((string) ($c['label'] ?? '')) === '' || trim((string) ($c['response_time'] ?? '')) === '') {
            $errors[] = 'contact requires label and response_time';
        }
        foreach ($r['tools'] ?? [] as $i => $t) {
            if (!empty($t['affiliate']) && empty($t['disclosed'])) {
                $errors[] = "tools[$i] affiliate link must have disclosed=true";
            }
        }
        $prices = array_column(is_array($r['packages'] ?? null) ? $r['packages'] : [], 'price');
        foreach ($r['listings'] ?? [] as $platform => $l) {
            if (!in_array($platform, Listings::PLATFORMS, true)) {
                $errors[] = "listing platform '$platform' is not supported";
            } elseif (!in_array($l['price'] ?? null, $prices, true)) {
                $errors[] = "listing '$platform' price must match a package price";
            }
        }
        return $errors;
    }
}
