<?php
use Hire\Render;
/** @var array $records */ /** @var string $title */
ob_start();
?>
<main>
<h1><?= Render::e($title) ?></h1>
<?php foreach ($records as $r): ?>
<p><a href="/<?= Render::e($r['slug']) ?>"><?= Render::e($r['headline']) ?></a></p>
<?php endforeach; ?>
</main>
<?php
$body = ob_get_clean();
require __DIR__ . '/layout.php';
