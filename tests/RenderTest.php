<?php
declare(strict_types=1);

namespace Hire\Tests;

use Hire\Render;
use PHPUnit\Framework\TestCase;

final class RenderTest extends TestCase
{
    public function test_page_has_exactly_one_contact_action(): void
    {
        $html = Render::page(Fixtures::record());
        $this->assertSame(1, substr_count($html, '<form'));
    }

    public function test_page_section_order(): void
    {
        $html = Render::page(Fixtures::record([
            'proof' => [['title' => 'Real item', 'detail' => 'Verified detail']],
            'tools' => [['name' => 'Tool', 'url' => 'https://tool.test', 'affiliate' => true, 'disclosed' => true]],
        ]));
        $ids = ['id="problem"', 'id="steps"', 'id="price"', 'id="proof"', 'id="contact"', 'id="tools"'];
        $last = -1;
        foreach ($ids as $id) {
            $pos = strpos($html, $id);
            $this->assertNotFalse($pos, "$id missing");
            $this->assertGreaterThan($last, $pos, "$id out of order");
            $last = $pos;
        }
    }

    public function test_empty_proof_renders_no_placeholder(): void
    {
        $html = Render::page(Fixtures::record(['proof' => []]));
        $this->assertStringNotContainsString('id="proof"', $html);
        $this->assertStringNotContainsString('coming soon', strtolower($html));
    }

    public function test_page_has_no_other_track_links(): void
    {
        $html = Render::page(Fixtures::record());
        $this->assertStringNotContainsString('help.wiredhowse.app', $html);
        $this->assertStringNotContainsString('hire-wiredhowse"', $html);
    }

    public function test_page_shows_price_and_response_time(): void
    {
        $html = Render::page(Fixtures::record());
        $this->assertStringContainsString('$150', $html);
        $this->assertStringContainsString('24 hours', $html);
    }

    public function test_affiliate_tool_is_disclosed_on_page(): void
    {
        $html = Render::page(Fixtures::record([
            'tools' => [['name' => 'Tool', 'url' => 'https://tool.test', 'affiliate' => true, 'disclosed' => true]],
        ]));
        $this->assertStringContainsString('affiliate link', strtolower($html));
    }

    public function test_output_is_html_escaped(): void
    {
        $html = Render::page(Fixtures::record(['headline' => '<script>alert(1)</script>']));
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }
}
