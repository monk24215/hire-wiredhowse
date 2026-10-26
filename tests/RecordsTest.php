<?php
declare(strict_types=1);

namespace Hire\Tests;

use Hire\Records;
use PHPUnit\Framework\TestCase;

final class RecordsTest extends TestCase
{
    public function test_valid_record_has_no_errors(): void
    {
        $this->assertSame([], Records::validate(Fixtures::record()));
    }

    public function test_validate_rejects_missing_price(): void
    {
        $r = Fixtures::record();
        unset($r['packages'][0]['price']);
        $errors = Records::validate($r);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('price', implode(' ', $errors));
    }

    public function test_validate_rejects_missing_contact(): void
    {
        $r = Fixtures::record();
        unset($r['contact']);
        $this->assertStringContainsString('contact', implode(' ', Records::validate($r)));
    }

    public function test_validate_rejects_undisclosed_affiliate(): void
    {
        $r = Fixtures::record(['tools' => [['name' => 'X', 'url' => 'https://x.test', 'affiliate' => true, 'disclosed' => false]]]);
        $this->assertStringContainsString('affiliate', implode(' ', Records::validate($r)));
    }

    public function test_validate_rejects_unknown_track(): void
    {
        $r = Fixtures::record(['track' => 'enterprise']);
        $this->assertStringContainsString('track', implode(' ', Records::validate($r)));
    }

    public function test_load_returns_only_valid_active_records_and_throws_on_invalid(): void
    {
        $dir = sys_get_temp_dir() . '/hire-test-' . uniqid();
        mkdir($dir);
        file_put_contents("$dir/a.json", json_encode(Fixtures::record()));
        $this->assertCount(1, Records::load($dir));

        $bad = Fixtures::record();
        unset($bad['packages']);
        file_put_contents("$dir/b.json", json_encode($bad));
        $this->expectException(\InvalidArgumentException::class);
        Records::load($dir);
    }

    public function test_load_skips_invalid_draft_without_throwing(): void
    {
        $dir = sys_get_temp_dir() . '/hire-test-' . uniqid();
        mkdir($dir);
        file_put_contents("$dir/a.json", json_encode(Fixtures::record()));
        $draft = Fixtures::record(['status' => 'draft', 'slug' => 'half-done']);
        unset($draft['packages']);
        file_put_contents("$dir/b.json", json_encode($draft));
        $records = Records::load($dir);
        $this->assertCount(1, $records);
        $this->assertSame('email-deliverability', $records[0]['slug']);
    }
}
