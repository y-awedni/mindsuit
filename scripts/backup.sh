#!/usr/bin/env bash
# Nightly backup: dump every database + sync uploads to object storage.
# Run from /opt/mindsuit on the VPS (e.g. via cron / systemd timer).
#
#   0 3 * * *  cd /opt/mindsuit && ./scripts/backup.sh >> /var/log/mindsuit-backup.log 2>&1
#
# Requires: docker compose stack running, rclone configured with a remote
# named "backup" (S3-compatible: Hetzner Storage Box, Backblaze B2, etc.).
set -euo pipefail

cd "$(dirname "$0")/.."

STAMP="$(date +%Y%m%d-%H%M%S)"
OUT="./backups/${STAMP}"
RETENTION_DAYS="${RETENTION_DAYS:-14}"
REMOTE="${BACKUP_REMOTE:-backup:mindsuit}"

mkdir -p "${OUT}"

echo "[backup] dumping all databases..."
# --all-databases captures the control DB and every tenant DB in one shot.
docker compose -f docker-compose.prod.yml exec -T database \
  sh -c 'exec mysqldump --single-transaction --quick --all-databases -uroot -p"$MARIADB_ROOT_PASSWORD"' \
  | gzip > "${OUT}/all-databases.sql.gz"

echo "[backup] archiving uploads..."
docker run --rm -v mindsuit_uploads:/data:ro -v "$(pwd)/${OUT}:/out" alpine \
  tar czf /out/uploads.tar.gz -C /data .

echo "[backup] syncing to object storage (${REMOTE})..."
if command -v rclone >/dev/null 2>&1; then
  rclone copy "${OUT}" "${REMOTE}/${STAMP}"
else
  echo "[backup] WARNING: rclone not installed; backup kept locally only."
fi

echo "[backup] pruning local backups older than ${RETENTION_DAYS} days..."
find ./backups -maxdepth 1 -type d -mtime "+${RETENTION_DAYS}" -exec rm -rf {} +

echo "[backup] done: ${OUT}"
