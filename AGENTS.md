# AGENTS.md

Personal book management app (BookShelf). Symfony 8.1, PHP >=8.4, Doctrine ORM/Migrations, Twig, MariaDB for dev.

## Commands

- Dev server: `symfony serve` (needs MariaDB up first, see DB below). Console: `php bin/console`
- Tests: `php bin/phpunit` runs the whole suite (functions, integration, and Panther E2E — nothing written yet). Focused run: `php bin/phpunit --filter SomeTest`
- Migrations: `php bin/console doctrine:migrations:migrate` (new DB or after pull). Migrations live in `migrations/` under namespace `DoctrineMigrations`
- No lint/static-analysis tooling is installed (no php-cs-fixer, no phpstan). Follow `.editorconfig` (4-space indent).

## Database

- Dev DB is **MariaDB/MySQL**, not the Postgres in `compose.yaml`. That file is the unused Symfony default — do not "fix" it.
- Start the dev DB with `./db.debug.ps1` (Docker, MariaDB 10.6, data in `_debugtmp/db`, creds in `.env`).
- `.env` `DATABASE_URL` pins `serverVersion=10.11.2-MariaDB` — keep the version pinned in sync with the real server.
- Tests use `app_test` (Doctrine appends `_test` suffix to the dbname in `when@test`). Create it before running tests: `CREATE DATABASE app_test` on the dev server, then `bin/console doctrine:migrations:migrate --env=test`.
- When in doubt, run `php bin/console doctrine:schema:validate`.

## Conventions / gotchas

- Entities use attribute mapping (`src/Entity`), underscore naming strategy. `make:*` (MakerBundle) is available — use `php bin/console make:entity` for entity/model changes, then generate a diff migration: `php bin/console doctrine:migrations:diff`.
- **Barcode scheme is load-bearing**: internal code = `CODE_TYPE + dbId + checksum`, implemented in `src/Utils/InternalCodeUtil.php` (type constants `00`..`04`). The object relation graph and barcode spec are documented in `README.md` — keep README and `InternalCodeUtil` in sync when either changes.
- Entity mutators and anything that invalidates the DB must be reflected in a new migration; the codebase has `failOnDeprecation/Notice/Warning` in `phpunit.dist.xml`, so deprecation-free botschmutz will fail tests.
- `SHELL_VERBOSITY=-1` and `APP_ENV=test` are forced by phpunit; tests bootstrap off `.env` so `.env.local` overrides apply.
- Panther is wired for E2E (geckodriver under `drivers/`, PANTHER_* keys in `.env.test`, Firefox at `/Applications/Firefox.app`) — **every implemented feature must ship with a Panther E2E test, run and passing** (see below).
- `APP_SHARE_DIR=var/share` — runtime-shared data dir; `var/` is gitignored.

## Frontend development

- **No-JS core, progressive enhancement only.** This app is used on ancient/limited clients: w3m, lynx, IE8, iOS 12 Safari, Kindle's embedded browser. EVERY core feature (CRUD, navigation, search, display) must be server-rendered and work with JavaScript disabled. JS (incl. Symfony UX / Stimulus components) is allowed only as a convenience layer on top — never required for a feature to function. Prefer plain Twig + Symfony forms/controllers over JS dependencies; never add a build step to make a feature work.
- Verify what you build renders usable without JS (e.g. check the HTML source / no-JS flow) — don't assume old-browser behavior from a modern-dev-rendered page.

## E2E tests (mandatory)

- Writing a feature or fixing a bug WITHOUT a passing Panther E2E test is an incomplete task. Implement the feature, write the E2E test covering the happy path (and key non-JS interaction), then run it: `php bin/phpunit --filter YourNewTest` (and the full suite before finishing).
- Prefer plain `PantherTestCase` against the real DB-backed app (`app_test`, see Database) over mocking; keep tests small and fast so the suite stays runnable. If a scenario can't run under Panther, say why instead of silently skipping.

## Symfony UX Frontend Stack

This project uses the Symfony UX frontend stack. Seven agent skills are installed to help you work with it.

For human: Original text data is hosted on: https://github.com/smnandre/symfony-ux-skills/blob/main/AGENTS.md?plain=1
Legal info: Original text and skills on following section are licensed under MIT License. See: https://github.com/smnandre/symfony-ux-skills/blob/main/LICENSE

### Which tool to use

- **Reusable static UI component** -- use the `twig-component` skill
- **Not sure which one fits** -- use the `symfony-ux` skill (orchestrator / decision tree)
