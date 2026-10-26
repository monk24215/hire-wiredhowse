<?php
declare(strict_types=1);

namespace Hire;

final class Listings
{
    public const PLATFORMS = ['upwork', 'freelancer', 'other'];

    /** @return array{title:string, description:string, price:string} */
    public static function forPlatform(array $record, string $platform): array
    {
        $l = $record['listings'][$platform] ?? null;
        if (!in_array($platform, self::PLATFORMS, true) || !is_array($l)) {
            throw new \InvalidArgumentException("No listing for platform '$platform'");
        }
        return ['title' => (string) $l['title'], 'description' => (string) $l['description'], 'price' => (string) $l['price']];
    }
}
