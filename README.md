# hire-wiredhowse

Individuals track of the for-hire web services operation.

- Audience: individuals. Live URL: `help.wiredhowse.app` (after DNS and deploy).
- Launch service: Website Rescue (`data/services/website-rescue.json`).
- One JSON record per service renders one single-page profile and the platform listings.
- No navigation to, or content for, the Small Business track (`hire-wiredhowse-builders`).
- Engine is copied unchanged from `hire-wiredhowse-builders`; only `TRACK` in `public/index.php` and the data differ.

Run tests: `composer install && vendor/bin/phpunit`
Run locally: `php -S 127.0.0.1:8080 public/index.php`
Print listings: `php bin/listings.php website-rescue`

This repo is public. Never commit leads (`data/leads.jsonl` is gitignored), credentials or private data.
Decisions: `docs/decisions.md`.
