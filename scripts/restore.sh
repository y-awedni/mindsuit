#!/usr/bin/env bash
# Restore a database dump into the running stack.
#   ./scripts/restore.sh backups/20260101-030000/all-databases.sql.gz
set -euo pipefail

cd "$(dirname "$0")/.."

DUMP="${1:?usage: restore.sh <path-to-dump.sql[.gz]>}"
COMPOSE="${COMPOSE_FILE:-docker-compose.prod.yml}"

echo "[restore] restoring ${DUMP} into the database container..."
if [[ "${DUMP}" == *.gz ]]; then
  gunzip -c "${DUMP}" | docker compose -f "${COMPOSE}" exec -T database \
    sh -c 'exec mysql -uroot -p"$MARIADB_ROOT_PASSWORD"'
else
  docker compose -f "${COMPOSE}" exec -T database \
    sh -c 'exec mysql -uroot -p"$MARIADB_ROOT_PASSWORD"' < "${DUMP}"
fi

echo "[restore] done."
