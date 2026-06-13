# Development

## Prerequisites

- Docker Desktop (or Docker Engine + Compose v2)
- Optional: `make` (on Windows, available via Git Bash, Chocolatey, or WSL)

## Start the stack

```bash
make init      # cp docker/.env.example docker/.env
make up        # docker compose up -d --build
make install   # composer install + assets:install
```

Without `make`, the equivalent commands are:

```bash
cp docker/.env.example docker/.env
docker compose up -d --build
docker compose exec php composer install --no-interaction --prefer-dist
docker compose exec php php bin/console assets:install web
```

## Services & ports

| Service  | URL / port                | Notes                                  |
| -------- | ------------------------- | -------------------------------------- |
| nginx    | http://localhost:8080     | App (prod front controller `app.php`)  |
| dev FC   | .../app_dev.php           | Symfony profiler & debug               |
| mailpit  | http://localhost:8025     | Captures all outbound email            |
| mysql    | localhost:3307            | MySQL 5.7; user/pass/db all `mindsuit` (dev only) |

## Seeding the database

The MySQL entrypoint auto-imports any `*.sql` / `*.sql.gz` in
`docker/mysql/init/` **the first time** the volume is created.

```bash
# Option A: seed on first boot
cp ~/Downloads/mindsuit-prod.sql docker/mysql/init/
make up

# Option B: import into an existing stack
make db-import f=~/Downloads/mindsuit-prod.sql

# Option C: wipe the volume and re-seed from docker/mysql/init/
make db-reset
```

Dumps are gitignored - never commit customer data.

## Common tasks

```bash
make console c="doctrine:schema:validate"   # run any console command
make cache                                  # clear+warm dev cache
make sh                                      # shell into the php container
make logs                                    # tail logs
make down                                    # stop
```

## Troubleshooting

- **`composer install` fails on `dev-master` packages** — the legacy
  `knplabs/*`, `liuggio/excelbundle` and `friendsofsymfony/user-bundle` refs may
  no longer resolve. This is expected and is the first task of the upgrade; see
  [UPGRADE.md](UPGRADE.md) step 1 (pin to real tags).
- **`apt-get` errors while building the PHP 7.2 image** — buster is EOL; the
  Dockerfile already rewrites sources to `archive.debian.org`.
- **Permission errors on `var/`** — `docker compose exec php chmod -R 0777 var`.
