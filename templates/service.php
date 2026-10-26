<?php
use Hire\Render;
/** @var array $r */
$e = fn($v) => Render::e($v);
ob_start();
?>
<main>
<section id="problem">
<h1><?= $e($r['headline']) ?></h1>
</section>

<section id="steps">
<h2>What I do</h2>
<ol>
<?php foreach ($r['steps'] as $step): ?>
<li><?= $e($step) ?></li>
<?php endforeach; ?>
</ol>
</section>

<section id="price">
<h2>Packages and price</h2>
<?php foreach ($r['packages'] as $p): ?>
<div class="pkg"><span><b><?= $e($p['name']) ?></b><?php if (!empty($p['detail'])): ?><br><span class="note"><?= $e($p['detail']) ?></span><?php endif; ?></span><span class="price"><?= $e($p['price']) ?></span></div>
<?php endforeach; ?>
</section>

<?php if (!empty($r['proof'])): ?>
<section id="proof">
<h2>Proof</h2>
<?php foreach ($r['proof'] as $item): ?>
<p><b><?= $e($item['title']) ?></b><br><?= $e($item['detail'] ?? '') ?></p>
<?php endforeach; ?>
</section>
<?php endif; ?>

<section id="contact">
<h2><?= $e($r['contact']['label']) ?></h2>
<form method="post" action="/contact">
<input type="hidden" name="slug" value="<?= $e($r['slug']) ?>">
<div class="hp"><label>Leave blank<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
<label for="name">Your name</label>
<input id="name" name="name" required maxlength="120">
<label for="email">Your email</label>
<input id="email" name="email" type="email" required maxlength="200">
<label for="message">What is happening?</label>
<textarea id="message" name="message" rows="5" required maxlength="4000"></textarea>
<button type="submit">Send</button>
<p class="note">Reply within <?= $e($r['contact']['response_time']) ?>.</p>
</form>
</section>

<?php if (!empty($r['tools'])): ?>
<section id="tools">
<h2>Tools that may help</h2>
<ul>
<?php foreach ($r['tools'] as $t): ?>
<li><a href="<?= $e($t['url']) ?>" rel="sponsored noopener"><?= $e($t['name']) ?></a><?php if (!empty($t['affiliate'])): ?> <span class="note">(affiliate link: I may earn a commission at no cost to you)</span><?php endif; ?></li>
<?php endforeach; ?>
</ul>
</section>
<?php endif; ?>
</main>
<?php
$body = ob_get_clean();
$title = $r['headline'];
require __DIR__ . '/layout.php';
