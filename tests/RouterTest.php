<?php
declare(strict_types=1);

namespace Hire\Tests;

use Hire\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function test_unknown_slug_returns_404(): void
    {
        $r = Router::resolve('/nope', [Fixtures::record()], 'small-business');
        $this->assertSame(404, $r['status']);
        $this->assertNull($r['record']);
    }

    public function test_known_slug_returns_record(): void
    {
        $r = Router::resolve('/email-deliverability', [Fixtures::record()], 'small-business');
        $this->assertSame(200, $r['status']);
        $this->assertSame('email-deliverability', $r['record']['slug']);
    }

    public function test_other_track_slug_returns_404(): void
    {
        $other = Fixtures::record(['track' => 'individuals', 'slug' => 'website-rescue']);
        $r = Router::resolve('/website-rescue', [$other], 'small-business');
        $this->assertSame(404, $r['status']);
    }

    public function test_path_traversal_returns_404(): void
    {
        $r = Router::resolve('/../etc/passwd', [Fixtures::record()], 'small-business');
        $this->assertSame(404, $r['status']);
    }

    public function test_root_returns_index(): void
    {
        $r = Router::resolve('/', [Fixtures::record()], 'small-business');
        $this->assertSame(200, $r['status']);
        $this->assertNull($r['record']);
    }
}
