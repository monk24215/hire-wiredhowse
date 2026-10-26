<?php /** @var string $title */ /** @var string $body */ ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= \Hire\Render::e($title) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400&family=JetBrains+Mono&display=swap" rel="stylesheet">
<style>
:root{--bg:#0b0f14;--panel:#121820;--text:#e6edf3;--muted:#9aa7b4;--cyan:#00b4d8;--red:#e8342a}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--text);font:400 17px/1.6 'DM Sans',sans-serif}
main{max-width:720px;margin:0 auto;padding:32px 16px 64px}
h1,h2{font-family:'Bebas Neue',sans-serif;letter-spacing:.02em;font-weight:400}
h1{font-size:2.6rem;line-height:1.1;margin:0 0 24px}
h2{font-size:1.5rem;color:var(--cyan);margin:40px 0 12px}
section{border-top:1px solid #1e2833}
ol{padding-left:22px}
.pkg{background:var(--panel);border-left:3px solid var(--cyan);padding:12px 16px;margin:10px 0;display:flex;justify-content:space-between;gap:16px}
.pkg b{font-weight:400}.price{white-space:nowrap;font-family:'JetBrains Mono',monospace;color:var(--cyan)}
label{display:block;margin:14px 0 4px;color:var(--muted)}
input,textarea{width:100%;padding:10px;background:var(--panel);color:var(--text);border:1px solid #2a3644;border-radius:4px;font:inherit}
button{margin-top:18px;background:var(--red);color:#fff;border:0;padding:12px 24px;font:inherit;border-radius:4px;cursor:pointer}
.note{color:var(--muted);font-size:.9rem}
a{color:var(--cyan)}
.hp{position:absolute;left:-9999px}
</style>
</head>
<body>
<?= $body ?>
</body>
</html>
