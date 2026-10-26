<?php
declare(strict_types=1);

namespace Hire\Tests;

use Hire\Records;
use Hire\Render;
use PHPUnit\Framework\TestCase;

final class DataTest extends TestCase
{
    private const TRACK = 'individuals';
    private const OTHER_URL = 'builders.wiredhowse.app';

    public function test_every_shipped_record_is_valid_and_on_this_track(): void
    {
        $records = Records::load(__DIR__ . '/../data/services');
        $this->assertNotEmpty($records);
        foreach ($records as $r) {
            $this->assertSame(self::TRACK, $r['track'], $r['slug']);
            $this->assertSame([], Records::validate($r), $r['slug']);
        }
    }

    public function test_shipped_pages_never_mention_the_other_track(): void
    {
        foreach (Records::load(__DIR__ . '/../data/services') as $r) {
            $this->assertStringNotContainsString(self::OTHER_URL, Render::page($r));
            $this->assertSame(1, substr_count(Render::page($r), '<form'), $r['slug']);
        }
    }
}
