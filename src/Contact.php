<?php
declare(strict_types=1);

namespace Hire;

final class Contact
{
    private const NOTIFY_TO = 'koubre@gmail.com';
    private const DEDUPE_SECONDS = 60;

    /**
     * @param ?string   $file   lead log path; defaults to data/leads.jsonl
     * @param ?callable $notify fn(array $lead): void; defaults to mail() to the owner
     * @param ?array    $allowedSlugs when set, the slug must be one of these
     * @return array{ok:bool, error:?string}
     */
    public static function handle(array $post, string $slug, ?string $file = null, ?callable $notify = null, ?int $now = null, ?array $allowedSlugs = null): array
    {
        $file ??= __DIR__ . '/../data/leads.jsonl';
        $now ??= time();

        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            return ['ok' => false, 'error' => 'Unknown service.'];
        }
        if ($allowedSlugs !== null && !in_array($slug, $allowedSlugs, true)) {
            return ['ok' => false, 'error' => 'Unknown service.'];
        }
        if (trim((string) ($post['website'] ?? '')) !== '') {
            return ['ok' => true, 'error' => null]; // honeypot: look successful, store nothing
        }
        $name = trim((string) ($post['name'] ?? ''));
        $email = trim((string) ($post['email'] ?? ''));
        $message = trim((string) ($post['message'] ?? ''));
        if ($name === '' || $email === '' || $message === '') {
            return ['ok' => false, 'error' => 'Please fill in your name, email and message.'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'Please enter a valid email address.'];
        }
        $lead = [
            'time' => $now,
            'slug' => $slug,
            'name' => mb_substr($name, 0, 120),
            'email' => mb_substr($email, 0, 200),
            'message' => mb_substr($message, 0, 4000),
        ];
        if (self::isDuplicate($file, $lead, $now)) {
            return ['ok' => true, 'error' => null];
        }
        file_put_contents($file, json_encode($lead, JSON_UNESCAPED_SLASHES) . "\n", FILE_APPEND | LOCK_EX);
        ($notify ?? [self::class, 'mailOwner'])($lead);
        return ['ok' => true, 'error' => null];
    }

    private static function isDuplicate(string $file, array $lead, int $now): bool
    {
        if (!is_file($file)) {
            return false;
        }
        foreach (array_reverse(file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: []) as $line) {
            $row = json_decode($line, true);
            if (!is_array($row) || $now - (int) $row['time'] >= self::DEDUPE_SECONDS) {
                break;
            }
            if ($row['email'] === $lead['email'] && $row['slug'] === $lead['slug']) {
                return true;
            }
        }
        return false;
    }

    /** @return array{from:string,to:string[],reply_to:string,subject:string,text:string} */
    public static function resendPayload(array $lead): array
    {
        return [
            'from' => 'WiredHowse Leads <leads@wiredhowse.app>',
            'to' => [self::NOTIFY_TO],
            'reply_to' => $lead['email'],
            'subject' => 'New lead: ' . $lead['slug'],
            'text' => "Name: {$lead['name']}\nEmail: {$lead['email']}\nService: {$lead['slug']}\n\n{$lead['message']}\n",
        ];
    }

    /** Sends through Resend when RESEND_API_KEY is set in the server environment; otherwise falls back to mail(). */
    public static function mailOwner(array $lead): void
    {
        $payload = self::resendPayload($lead);
        $key = getenv('RESEND_API_KEY');
        if ($key && function_exists('curl_init')) {
            $ch = curl_init('https://api.resend.com/emails');
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $key, 'Content-Type: application/json'],
                CURLOPT_POSTFIELDS => json_encode($payload),
            ]);
            curl_exec($ch);
            curl_close($ch);
            return;
        }
        @mail(self::NOTIFY_TO, $payload['subject'], $payload['text'], 'Reply-To: ' . $payload['reply_to']);
    }
}
