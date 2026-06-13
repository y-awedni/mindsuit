# Mindsuit

Mindsuit is a French-language ERP for small and medium businesses: quotes
(devis), invoices (factures), delivery notes (bons de livraison), purchasing,
stock, payments (règlements), treasury and reporting. It is being revived from a
2020-era Symfony 3.2 application into a maintainable, multi-tenant SaaS.

> **Status:** Phase 0-1 (cleanup + reproducible dev env) are in place. The
> framework upgrade and SaaS layer are planned and documented under [`docs/`](docs/).
> See the [roadmap](#roadmap) below.

## Tech stack

| Layer        | Current (legacy)              | Target (post-upgrade)            |
| ------------ | ----------------------------- | -------------------------------- |
| Language     | PHP 7.2                       | PHP 8.3                          |
| Framework    | Symfony 3.2                   | Symfony 6.4 LTS                  |
| ORM          | Doctrine ORM 2.5              | Doctrine ORM 2.x + Migrations    |
| Auth         | FOSUserBundle                 | Native Symfony Security          |
| Templating   | Twig + static Bootstrap/sb-admin-2 | Twig + AssetMapper          |
| Database     | MySQL 5.7                     | MySQL 8.0 (DB-per-tenant)        |
| Deployment   | cPanel / Apache               | Docker Compose on a VPS + Caddy  |

## Quickstart (local dev)

Requires Docker. The dev stack runs the legacy app on PHP 7.2 so behaviour
matches the original host while we modernize it.

```bash
make init          # creates docker/.env from the example
make up            # build + start php, nginx, mysql, mailpit
make install       # composer install + install web assets
```

Then open:

- App: <http://localhost:8080>
- Dev front controller (profiler): <http://localhost:8080/app_dev.php>
- Mailpit (captured emails): <http://localhost:8025>

To seed real data, drop a dump into `docker/mysql/init/` before `make up`, or
import later with `make db-import f=path/to/dump.sql`. See
[`docs/DEVELOPMENT.md`](docs/DEVELOPMENT.md).

> On Windows without `make`, run the underlying `docker compose` commands
> directly (documented in [`docs/DEVELOPMENT.md`](docs/DEVELOPMENT.md)).

## Repository layout

```
app/            Symfony 3 kernel, config, FOSUser overrides, templates
src/AppBundle/  Application code (entities, controllers, forms, listeners, services)
web/            Front controllers (app.php, app_dev.php) + public assets
bin/, var/      Console + runtime (cache/logs/sessions are gitignored)
docker/         Dev image, nginx, php.ini, db seed dir
infra/          Production Caddy reverse proxy (wildcard TLS)
scripts/        Backup / restore helpers
docs/           Upgrade runbook, deployment guide, SaaS architecture
.github/        CI (lint + build + smoke) and Deploy (build -> GHCR -> VPS)
```

## Roadmap

The full plan lives in the Cursor plan file. Working docs:

1. **Cleanup & fresh repo** - done (this commit).
2. **Reproducible dev env + baseline** - `docker-compose.yml`, CI, migrations baseline.
3. **Framework upgrade 3.2 -> 6.4 / PHP 8.3** - [`docs/UPGRADE.md`](docs/UPGRADE.md).
4. **Production infra** - [`docs/DEPLOYMENT.md`](docs/DEPLOYMENT.md).
5. **SaaS layer (DB-per-tenant, billing)** - [`docs/SAAS_ARCHITECTURE.md`](docs/SAAS_ARCHITECTURE.md).

## Security note

The previous host leaked database and SMTP credentials in `web/error_log`
(now deleted). **Rotate those credentials** on the database and mail servers
before reusing them anywhere. Secrets live only in gitignored files
(`app/config/parameters.yml`, `docker/.env`, server `.env`) - never commit them.

## License

Proprietary. All rights reserved.
