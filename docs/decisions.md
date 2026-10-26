# Decisions (Task 1)

Date: 2026-10-25
Status: prices, hosting and notification chosen by Claude on owner's instruction. Proof and the existing-pattern read are still open.

## 1. Repo access
- Repos exist: `monk24215/hire-wiredhowse-builders` (Small Business), `monk24215/hire-wiredhowse` (Individuals). Both public: no leads, credentials or private data are ever committed.
- OPEN: the GitHub connection can read but not write (403). Push from the git bundles, or grant the Claude GitHub app Contents read and write on both repos.

## 2. Prices and packages (introductory, set below the market to win first jobs and reviews)

Basis: postings seen on Upwork, Oct 25, 2026 (deliverability fixes $10-$750 fixed; 10 domains / 30 mailboxes $750; malware cleanup of 5 sites $150; WordPress speed/tracking fix $150; plugin and migration projects $400).

Small Business, Email Deliverability & Domain Setup
- Audit and Fix: $150 (one domain; diagnose spam placement and authentication, fix SPF/DKIM/DMARC, written report)
- Domain and Mailbox Setup: $350 (up to 3 domains and 10 mailboxes, fully authenticated and tested)
- Monthly Monitoring: $250 per month (weekly reputation and authentication check, alerts, one fix per month)

Individuals, Website Rescue
- Emergency Fix / Malware Cleanup: $150 (one site)
- Speed and Tracking Fix: $120
- Migration or Plugin Rebuild: quoted, from $300

Raise rule: raise prices after the first 5 completed jobs with reviews, then review again every 30 days against the tracker.

Revenue check at these prices: 8 audits ($1,200) + 4 setups ($1,400) + 6 monitoring retainers ($1,500) + 12 rescue jobs ($1,800) = $5,900 per month, the low end of the target. Reaching the middle of the range depends on retainers and on the price raise.

## 3. Hosting and URLs
- Existing app server, existing PHP pattern, no Railway.
- Small Business: `builders.wiredhowse.app`
- Individuals: `help.wiredhowse.app`
- Kept separate from `hire.wiredhowse.app`, which is the role portfolio and serves a different purpose.
- DNS done: A records for both subdomains point to 65.21.62.111 (Cloudflare, DNS only, not proxied). That server also hosts plane and wpress. The Mailcow mail server is separate (5.78.204.93) and is not touched.
- OPEN: PHP, a virtual host per subdomain with docroot `public/`, and HTTPS on 65.21.62.111.

## 4. Proof inventory
- OPEN (owner). Real items only. Pages launch without a proof section until items are supplied; no results are invented.

## 5. Lead notification
- Contact submission is written to `data/leads.jsonl` (gitignored) and emailed to koubre@gmail.com through Resend (domain wiredhowse.app, verified), sent as leads@wiredhowse.app. Set `RESEND_API_KEY` in the server environment; never commit it. Without it the code falls back to PHP `mail()`. Response time stated on every page: 24 hours.
- NOT YET VERIFIED: unit tests pass, but no real submission has been sent from a deployed page. Plan Task 4 Step 5 stays open until one real test lead reaches koubre@gmail.com.

## 6. Existing hire.wiredhowse.app pattern
- Owner confirmed there is no existing code to follow. The engine was built from the described pattern: data-driven PHP, one JSON record per page, isolated pages, no cross-navigation.
