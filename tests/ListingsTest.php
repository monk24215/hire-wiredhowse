<?php
declare(strict_types=1);

namespace Hire\Tests;

use Hire\Listings;
use PHPUnit\Framework\TestCase;

final class ListingsTest extends TestCase
{
    public function test_listing_title_comes_from_record(): void
    {
        foreach (['upwork', 'freelancer', 'other'] as $p) {
            $r = Fixtures::record(['listings' => [$p => ['title' => "T-$p", 'description' => 'D', 'price' => '$150']]]);
            $this->assertSame("T-$p", Listings::forPlatform($r, $p)['title']);
        }
    }

    public function test_listing_price_matches_record_package(): void
    {
        $r = Fixtures::record(['listings' => ['upwork' => ['title' => 'T', 'description' => 'D', 'price' => '$150']]]);
        $prices = array_column($r['packages'], 'price');
        $this->assertContains(Listings::forPlatform($r, 'upwork')['price'], $prices);
    }

    public function test_unknown_platform_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Listings::forPlatform(Fixtures::record(), 'nowhere');
    }

    public function test_validate_rejects_listing_price_not_in_packages(): void
    {
        $r = Fixtures::record(['listings' => ['upwork' => ['title' => 'T', 'description' => 'D', 'price' => '$999']]]);
        $this->assertStringContainsString('listing', implode(' ', \Hire\Records::validate($r)));
    }
}
