# 🤖 Autonomous AI Blog (Laravel 13 + Claude)

A professional, zero-human-touch publishing platform. Claude AI uses live
**web_search** to find trending topics, researches them, and writes long,
comprehensive, SEO + AI-search optimized articles **with images and ad slots** —
then schedules and publishes them automatically. Includes a full **admin panel**.

## Highlights
- **Comprehensive articles** — 1,400–2,200 words, 4–5 min read, Key Takeaways,
  data tables, 5–7 FAQ, written to rank on Google *and* be cited by AI search.
- **Images, keyless** — every post gets an AI-generated hero + inline images via
  Pollinations (`[IMAGE: ...]` markers Claude writes → responsive `<figure>`s).
- **Google AdSense ready** — ad slots auto-inserted (leaderboard, in-feed,
  between article sections, sidebar). Shows labelled placeholders until you add
  your publisher ID, so spacing is correct from day one.
- **Daily market coverage** — dedicated market-open (9:30 AM IST) and
  market-close (4:00 PM IST) research posts with live index data.
- **Professional, mobile-responsive design** — magazine layout, hero, trending
  sidebar, category nav, search.
- **Admin panel** at `/admin` — search, filter (category/status/type/sort),
  publish/unpublish, feature, delete, and trigger generation. No manual writing.

## Stack
- Laravel 13 (PHP 8.3+), SQLite (default), Anthropic PHP SDK
- Model `claude-opus-4-8` + server-side `web_search` tool

## Setup
```bash
composer install
# .env already has APP_KEY + SQLite. Set these:
#   ANTHROPIC_API_KEY=sk-ant-...      (from https://console.anthropic.com)
#   ADMIN_PASSWORD=your-secret
php artisan migrate
php artisan serve
```
- Public site: http://127.0.0.1:8000
- Admin panel: http://127.0.0.1:8000/admin

## Google AdSense (auto income)
Once approved, fill these in `.env` and ads go live automatically:
```
ADSENSE_CLIENT=ca-pub-XXXXXXXXXXXXXXXX
ADSENSE_SLOT_LEADERBOARD=1234567890
ADSENSE_SLOT_IN_ARTICLE=2345678901
ADSENSE_SLOT_SIDEBAR=3456789012
```
Ad placement & cadence: `config/blog.php`.

## Run it manually
```bash
php artisan blogify:generate-posts                 # today's 8 trending articles
php artisan blogify:generate-posts --market=open   # market opening briefing
php artisan blogify:generate-posts --market=close  # market closing wrap
php artisan blogify:publish-due                     # publish posts whose time arrived
```

## Make it fully autonomous
One OS cron entry runs Laravel's scheduler (daily 12 PM IST batch, weekday
market open/close posts, and 5-min publisher):
```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## Key files
| Piece | File |
|---|---|
| AI wrapper + master prompt + web_search | `app/Services/ClaudeService.php` |
| Generate (general + market modes) | `app/Console/Commands/GenerateBlogPosts.php` |
| Publish due posts | `app/Console/Commands/PublishScheduledPosts.php` |
| Image + ad injection | `app/Support/ArticleRenderer.php` |
| Schedule (12 PM + market + publisher) | `routes/console.php` |You have write blogs on all main topics our website is a one type of 
| Public site | `app/Http/Controllers/BlogController.php`, `resources/views/blog/*`, `resources/views/layouts/app.blade.php` |
| Admin panel | `app/Http/Controllers/Admin/AdminController.php`, `app/Http/Middleware/AdminAuth.php`, `resources/views/admin/*` |
| Config | `config/blog.php` (admin password, images, AdSense, read time) |

## Notes
- Images are keyless (Pollinations). Swap the endpoint in `config/blog.php` to
  use any other text-to-image provider.
- Article HTML is constrained by the system prompt to safe tags. If you loosen
  that, sanitize before rendering with `{!! !!}`.
- Admin auth is a single shared `ADMIN_PASSWORD` (session-gated). For multi-user
  auth, swap in Laravel Breeze/Fortify.
- Switch to MySQL via `DB_CONNECTION=mysql` (+ credentials) and `php artisan migrate`.
