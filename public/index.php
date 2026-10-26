<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Hire\Records;
use Hire\Render;
use Hire\Router;

const TRACK = 'individuals';
const TRACK_TITLE = 'Help with your website';

$records = Records::load(__DIR__ . '/../data/services');
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if ($path === '/contact' && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $slugs = array_column(array_filter($records, fn($r) => $r['track'] === TRACK), 'slug');
    $result = \Hire\Contact::handle($_POST, (string) ($_POST['slug'] ?? ''), null, null, null, $slugs);
    http_response_code($result['ok'] ? 200 : 400);
    echo Render::layout(
        $result['ok'] ? 'Thank you' : 'Please check your message',
        '<main><h1>' . Render::e($result['ok'] ? 'Thank you' : 'Please check your message') . '</h1><p>'
        . Render::e($result['ok'] ? 'I will reply soon.' : (string) $result['error']) . '</p></main>'
    );
    exit;
}

$route = Router::resolve($path, $records, TRACK);
http_response_code($route['status']);
if ($route['status'] === 404) {
    echo Render::notFound();
} elseif ($route['record'] === null) {
    echo Render::index(array_values(array_filter($records, fn($r) => $r['track'] === TRACK)), TRACK_TITLE);
} else {
    echo Render::page($route['record']);
}
