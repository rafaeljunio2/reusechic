#!/bin/bash
set -e

DB_HOST="${DB_HOST:-localhost}"
DB_NAME="${DB_NAME:-reusechic}"
DB_USER="${DB_USER:-root}"
DB_PASS="${DB_PASS:-}"

echo "Waiting for MySQL at $DB_HOST..."
until mysql -h"$DB_HOST" -u"$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -e "SELECT 1" &>/dev/null; do
  sleep 2
done
echo "MySQL is ready."

# Import schema (all statements use IF NOT EXISTS — safe to run repeatedly)
mysql -h"$DB_HOST" -u"$DB_USER" ${DB_PASS:+-p"$DB_PASS"} < /var/www/html/sql/reusechic.sql
echo "Schema imported."

exec "$@"
