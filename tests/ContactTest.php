<?php
declare(strict_types=1);

namespace Hire\Tests;

use Hire\Contact;
use PHPUnit\Framework\TestCase;

final class ContactTest extends TestCase
{
    private string $file;
    private int $notified = 0;

    protected function setUp(): void
    {
        $this->file = sys_get_temp_dir() . '/leads-' . uniqid() . '.jsonl';
        $this->notified = 0;
    }

    private function post(array $o = []): array
    {
        return array_merge(['name' => 'Pat', 'email' => 'pat@example.com', 'message' => 'Emails go to spam', 'website' => ''], $o);
    }

    private function handle(array $post, int $now = 1000): array
    {
        return Contact::handle($post, 'email-deliverability', $this->file, function () { $this->notified++; }, $now);
    }

    private function rows(): int
    {
        return is_file($this->file) ? count(file($this->file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) : 0;
    }

    public function test_blank_fields_rejected(): void
    {
        foreach (['name', 'email', 'message'] as $f) {
            $r = $this->handle($this->post([$f => '  ']));
            $this->assertFalse($r['ok'], $f);
        }
        $this->assertSame(0, $this->rows());
    }

    public function test_bad_email_rejected(): void
    {
        $this->assertFalse($this->handle($this->post(['email' => 'not-an-email']))['ok']);
        $this->assertSame(0, $this->rows());
    }

    public function test_valid_submission_appends_one_row_and_notifies(): void
    {
        $r = $this->handle($this->post());
        $this->assertTrue($r['ok']);
        $this->assertSame(1, $this->rows());
        $this->assertSame(1, $this->notified);
        $row = json_decode(trim((string) file($this->file)[0]), true);
        $this->assertSame('email-deliverability', $row['slug']);
        $this->assertSame('pat@example.com', $row['email']);
    }

    public function test_duplicate_within_60s_ignored(): void
    {
        $this->handle($this->post(), 1000);
        $r = $this->handle($this->post(), 1030);
        $this->assertTrue($r['ok']);
        $this->assertSame(1, $this->rows());
        $this->assertSame(1, $this->notified);
    }

    public function test_same_lead_after_60s_is_new(): void
    {
        $this->handle($this->post(), 1000);
        $this->handle($this->post(), 1061);
        $this->assertSame(2, $this->rows());
    }

    public function test_honeypot_creates_no_row(): void
    {
        $r = $this->handle($this->post(['website' => 'http://spam.test']));
        $this->assertTrue($r['ok']);
        $this->assertSame(0, $this->rows());
        $this->assertSame(0, $this->notified);
    }

    public function test_bad_slug_rejected(): void
    {
        $r = Contact::handle($this->post(), '../x', $this->file, function () {}, 1000);
        $this->assertFalse($r['ok']);
    }

    public function test_slug_not_in_allowed_list_rejected(): void
    {
        $r = Contact::handle($this->post(), 'website-rescue', $this->file, function () { $this->notified++; }, 1000, ['email-deliverability']);
        $this->assertFalse($r['ok']);
        $this->assertSame(0, $this->rows());
        $this->assertSame(0, $this->notified);
    }

    public function test_slug_in_allowed_list_accepted(): void
    {
        $r = Contact::handle($this->post(), 'email-deliverability', $this->file, function () { $this->notified++; }, 1000, ['email-deliverability']);
        $this->assertTrue($r['ok']);
        $this->assertSame(1, $this->rows());
    }

    public function test_resend_payload_targets_owner_from_wiredhowse_domain(): void
    {
        $lead = ['time' => 1000, 'slug' => 'email-deliverability', 'name' => 'Pat', 'email' => 'pat@example.com', 'message' => 'Emails go to spam'];
        $p = Contact::resendPayload($lead);
        $this->assertSame(['koubre@gmail.com'], $p['to']);
        $this->assertStringEndsWith('@wiredhowse.app>', $p['from']);
        $this->assertSame('pat@example.com', $p['reply_to']);
        $this->assertStringContainsString('email-deliverability', $p['subject']);
        $this->assertStringContainsString('Emails go to spam', $p['text']);
    }
}
