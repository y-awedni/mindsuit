# Deployment

Production runs as a Docker Compose stack on a single VPS, fronted by Caddy
(automatic TLS, including wildcard certs for tenant subdomains). No Kubernetes -
Compose on one VPS scales well past the initial SaaS needs; revisit only when
tenant count demands it.

> Activates after the framework upgrade ([UPGRADE.md](UPGRADE.md)), because the
> production image targets PHP 8.3 / Symfony 6.4.

## 1. Provision the VPS

- A 4 vCPU / 8 GB host (Hetzner CPX31, OVH, etc.) is plenty to start.
- Install Docker Engine + Compose plugin.
- Create `/opt/mindsuit`, clone the repo there, and create a deploy user with
  SSH key access.

```bash
ssh root@vps
mkdir -p /opt/mindsuit && cd /opt/mindsuit
git clone <repo-url> .
```

## 2. DNS

Point both the apex and a wildcard at the VPS:

```
A     minduos.com        -> <vps-ip>
A     *.minduos.com      -> <vps-ip>
```

Wildcard TLS uses DNS-01, so create a scoped API token at your DNS provider
(the `infra/Dockerfile.caddy` example uses Cloudflare).

## 3. Server-side secrets (`/opt/mindsuit/.env`)

Never committed. Create it on the server:

```ini
# Caddy / TLS
ACME_EMAIL=ops@minduos.com
CF_API_TOKEN=<cloudflare-scoped-token>

# App
APP_ENV=prod
APP_SECRET=<openssl rand -hex 32>
DATABASE_URL=mysql://mindsuit:<pass>@database:3306/mindsuit_control?serverVersion=mariadb-10.6
MAILER_DSN=smtp://<user>:<pass>@<smtp-host>:587

# MariaDB
MARIADB_ROOT_PASSWORD=<strong>
MARIADB_DATABASE=mindsuit_control
MARIADB_USER=mindsuit
MARIADB_PASSWORD=<strong>
```

## 4. First boot

```bash
cd /opt/mindsuit
docker compose -f docker-compose.prod.yml up -d database
docker compose -f docker-compose.prod.yml build caddy
# Pull the app image built by CI (or build locally for the first run):
echo "APP_IMAGE=ghcr.io/<owner>/<repo>:latest" > .image.env
docker compose --env-file .env --env-file .image.env -f docker-compose.prod.yml up -d
docker compose -f docker-compose.prod.yml exec php php bin/console doctrine:migrations:migrate --no-interaction
```

## 5. Continuous deployment

`.github/workflows/deploy.yml` runs on push to `main`:

1. builds `docker/php/Dockerfile.prod`, pushes to GHCR;
2. SSHes to the VPS, pulls the new image, `up -d`, runs migrations (control DB +
   every tenant DB via `tenants:migrate`).

Required repo secrets: `SSH_HOST`, `SSH_USER`, `SSH_KEY`. GHCR auth uses the
built-in `GITHUB_TOKEN`.

## 6. Backups

`scripts/backup.sh` dumps all databases + archives uploads and pushes to
object storage via rclone. Schedule it nightly:

```bash
# crontab -e
0 3 * * * cd /opt/mindsuit && ./scripts/backup.sh >> /var/log/mindsuit-backup.log 2>&1
```

Test a restore quarterly with `scripts/restore.sh`.

## 7. Observability

- **Errors**: add `sentry/sentry-symfony`; set `SENTRY_DSN` in `.env`.
- **Logs**: Monolog -> stdout, captured by `docker compose logs` / your log driver.
- **Uptime**: an external monitor (UptimeRobot / BetterStack) on `https://minduos.com`.

## Environments

| Env   | Where                         | Front controller | OPcache timestamps |
| ----- | ----------------------------- | ---------------- | ------------------ |
| dev   | `docker-compose.yml`          | `app_dev.php`    | revalidate (on)    |
| test  | CI                            | n/a              | n/a                |
| prod  | `docker-compose.prod.yml`     | `index.php`      | off (deploy=image) |
