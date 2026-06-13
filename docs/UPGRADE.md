# Upgrade runbook: Symfony 3.2 -> 6.4 LTS / PHP 8.3

This is an **incremental** upgrade. The app must boot and pass the smoke test at
the end of every step before moving on. Work on a branch, commit per step, and
keep the dev stack running for verification.

> Why incremental: skipping LTS hops (3.4, 4.4, 5.4) multiplies breakage. Each
> hop has an official upgrade guide and Rector rule set.

## Prerequisites (do these first)

### A. Get the baseline green

1. `make up && make install` and confirm the app loads against a production DB
   dump (see [DEVELOPMENT.md](DEVELOPMENT.md)).
2. Click through the core flows: create a devis -> convert to facture -> add a
   règlement; create a stock movement; open the stats pages. These are the
   acceptance checks for the whole upgrade.

### B. Establish a migrations baseline

The schema is currently managed ad-hoc. Before upgrading, capture it:

```bash
make console c="doctrine:schema:validate"          # see drift
composer require doctrine/doctrine-migrations-bundle  # add the bundle
make console c="doctrine:migrations:diff"          # generate baseline migration
# Mark it as already-applied on the existing DB:
make console c="doctrine:migrations:version --add --all --no-interaction"
```

Commit the baseline migration. From now on, **all** schema changes go through
migrations - this is also a prerequisite for tenant provisioning (Phase 4).

### C. Add Rector (automates most mechanical changes)

```bash
composer require --dev rector/rector
```

Use `rector/rector` with the `SetList::SYMFONY_*` and `LevelSetList::UP_TO_PHP_*`
rule sets, one target at a time. See `rector.php` (added in step 2).

---

## Step 1 - Symfony 3.2 -> 3.4 LTS (pin the dead deps)

The blocker today is `dev-master` / `@dev` dependencies that no longer resolve.
Pin them to their final tagged releases, then bump Symfony.

In `composer.json`:

| Package                            | From         | To (suggested)        |
| ---------------------------------- | ------------ | --------------------- |
| `symfony/symfony`                  | `3.2.*`      | `3.4.*`               |
| `friendsofsymfony/user-bundle`     | `~2.0@dev`   | `^2.1`                |
| `knplabs/knp-paginator-bundle`     | `dev-master` | `^2.8`                |
| `knplabs/knp-time-bundle`          | `dev-master` | `^1.11`               |
| `liuggio/excelbundle`              | `dev-master` | (replaced in step 4)  |
| `sensio/framework-extra-bundle`    | `^3.0.2`     | `^5.1`                |

```bash
composer update
make console c="cache:clear"
```

Fix deprecations surfaced by `symfony/phpunit-bridge`. Re-run the acceptance
checks. Commit.

## Step 2 - Symfony 3.4 -> 4.4 LTS (the big restructure)

This is the largest step: directory layout and front controllers change.

1. **Layout migration** (Symfony Flex conventions):
   - `app/config/*` -> `config/` (packages split under `config/packages/`)
   - `app/Resources/views/` -> `templates/`
   - `web/` -> `public/`; `web/app.php` -> `public/index.php`
   - `var/logs` -> `var/log`
   - `AppBundle\` namespace -> `App\` (PSR-4 root `App\` => `src/`), drop the bundle
   - `app/AppKernel.php` -> `src/Kernel.php`
2. Adopt **Symfony Flex** and migrate config to `config/packages/*.yaml` +
   `config/services.yaml` (autowiring/autoconfiguration).
3. Replace `parameters.yml` with `.env` + `config/services.yaml` bound params,
   and a single `DATABASE_URL` / `MAILER_DSN`.
4. Bump PHP to 7.4 in `docker/php/Dockerfile`.
5. Run Rector `SYMFONY_44` + `SYMFONY_CODE_QUALITY` sets; convert routing
   annotations as needed.

Update the dev `docker/nginx/default.conf` document root to `public/` and the
front controller to `index.php`. Update `infra/Caddyfile` likewise. Commit.

## Step 3 - Symfony 4.4 -> 5.4 -> 6.4 LTS / PHP 8.3

Mostly mechanical with Rector, one hop at a time:

```bash
# per hop: bump symfony/* constraint, then
composer update "symfony/*" --with-all-dependencies
vendor/bin/rector process            # SYMFONY_54 / SYMFONY_64, UP_TO_PHP_83
make console c="cache:clear"
```

- Convert annotations to **PHP 8 attributes** (`#[Route]`, `#[ORM\Entity]`).
- Add property types / return types (Rector `TYPE_DECLARATION`).
- Bump base image to `php:8.3-fpm` (now `docker/php/Dockerfile.prod` matches).

## Step 4 - Replace dead libraries

Do these as you reach a compatible Symfony version (4.4+):

| Legacy                              | Replacement                         | Notes                                                                 |
| ----------------------------------- | ----------------------------------- | --------------------------------------------------------------------- |
| FOSUserBundle                       | Native Security (`make:user`, `make:registration-form`, `make:reset-password`) | Migrate the `fos_user` table; keep bcrypt hashes (they verify under `auto`/`bcrypt`). |
| SwiftMailer + swiftmailer-bundle    | `symfony/mailer`                    | `MAILER_DSN`; replace `\Swift_Message` usage.                          |
| `liuggio/excelbundle` (PHPExcel)    | `phpoffice/phpspreadsheet`          | Keep the `ExcelCustomConfig` styling helper's public API; swap internals. |
| Static assets in `Resources/Public` | **AssetMapper** (`symfony/asset-mapper`) | No Node toolchain; `importmap` + `asset-map:compile`.             |
| `sensio/distribution-bundle`, `generator-bundle` | remove                 | Obsolete under Flex.                                                   |

## Step 5 - Quality net (proportionate)

```bash
composer require --dev phpstan/phpstan phpunit/phpunit friendsofphp/php-cs-fixer
```

- **PHPStan** at level 3-5 with a baseline (`phpstan analyse --generate-baseline`).
- **PHP-CS-Fixer** with `@Symfony` ruleset; run in CI.
- **Route smoke test**: a single functional test that logs in and GETs every
  route from the router, asserting non-5xx. Cheap, catches most regressions
  across the 49 controllers.
- **EventListener tests**: the real business logic lives in
  `src/.../EventListener/` (stock updates, code generation, withholding). Add
  focused functional tests there.

Promote the `smoke` CI job to required once it is green (remove
`continue-on-error` in `.github/workflows/ci.yml`).

## Definition of done

- App runs on PHP 8.3 / Symfony 6.4, all acceptance flows pass.
- `parameters.yml` is gone; config is `.env` + `config/`.
- FOSUserBundle, SwiftMailer, PHPExcel, Assetic, distribution/generator bundles
  removed from `composer.json`.
- CI lint + build + smoke all green and required.
- `docker/php/Dockerfile.prod` builds successfully.
