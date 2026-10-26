<?php
declare(strict_types=1);

namespace Hire\Tests;

final class Fixtures
{
    public static function record(array $override = []): array
    {
        return array_replace_recursive([
            'track' => 'small-business',
            'slug' => 'email-deliverability',
            'status' => 'active',
            'headline' => 'My business emails are going to spam',
            'steps' => ['Check your setup', 'Fix authentication', 'Test and report'],
            'packages' => [['name' => 'Audit and Fix', 'price' => '$150']],
            'proof' => [],
            'contact' => ['label' => 'Describe your problem', 'response_time' => '24 hours'],
            'tools' => [],
            'listings' => [
                'upwork' => ['title' => 'Email deliverability fix', 'description' => 'Fix spam placement.', 'price' => '$150'],
            ],
        ], $override);
    }
}
