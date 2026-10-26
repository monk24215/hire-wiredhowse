<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Hire\Listings;
use Hire\Records;

$slug = $argv[1] ?? '';
foreach (Records::load(__DIR__ . '/../data/services') as $r) {
    if ($r['slug'] !== $slug) {
        continue;
    }
    foreach (Listings::PLATFORMS as $p) {
        if (!isset($r['listings'][$p])) {
            continue;
        }
        $l = Listings::forPlatform($r, $p);
        echo "=== " . strtoupper($p) . " ===\nTITLE: {$l['title']}\nPRICE: {$l['price']}\nDESCRIPTION:\n{$l['description']}\n\n";
    }
    exit(0);
}
fwrite(STDERR, "No active service with slug '$slug'\n");
exit(1);
