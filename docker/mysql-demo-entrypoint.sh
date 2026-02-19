#!/usr/bin/env bash
set -euo pipefail

# Path to official entrypoint (on official mysql images)
OFFICIAL_ENTRYPOINT="/usr/local/bin/docker-entrypoint.sh"

# Env (can be overridden):
: "${DEMO_DATABASE_LOCATION:=}"
: "${DEMO_DB_TARGET:=/docker-entrypoint-initdb.d/ilios.sql.gz}"

# MySQL datadir used by the image
DATADIR="${MYSQL_DATADIR:-/var/lib/mysql}"

log() { printf '%s\n' "$*"; }

download_demo_db() {
  # If no URL provided, skip
  if [ -z "${DEMO_DATABASE_LOCATION}" ]; then
    log "No DEMO_DATABASE_LOCATION provided; skipping demo DB download."
    return 1
  fi

  # create target dir if necessary
  mkdir -p "$(dirname "${DEMO_DB_TARGET}")"

  # attempt download using curl then wget
  log "Downloading demo DB from ${DEMO_DATABASE_LOCATION} to ${DEMO_DB_TARGET} ..."
  if command -v curl >/dev/null 2>&1; then
    curl -fSL "${DEMO_DATABASE_LOCATION}" -o "${DEMO_DB_TARGET}"
    rc=$?
  else
    log "ERROR: curl not available in image; cannot download demo DB."
    return 2
  fi

  if [ "${rc:-1}" -ne 0 ]; then
    log "WARNING: demo DB download failed (exit ${rc}); proceeding without demo DB."
    [ -f "${DEMO_DB_TARGET}" ] && rm -f "${DEMO_DB_TARGET}"
    return 3
  fi

  log "Download complete. (size=$(stat -c%s "${DEMO_DB_TARGET}" 2>/dev/null || echo 'unknown'))"
  return 0
}

# Main behavior:
# 1. if the datadir not initialized -> download demo DB into /docker-entrypoint-initdb.d/ so official entrypoint imports it
# 2. exec the official docker-entrypoint.sh with the passed args (e.g. "mysqld")
#
# IMPORTANT: the official entrypoint expects to be executed as PID1.
#           we use exec so the original script receives signals normally.

if ! command -v "${OFFICIAL_ENTRYPOINT}" >/dev/null 2>&1; then
  log "ERROR: official entrypoint (${OFFICIAL_ENTRYPOINT}) not found in image."
  exit 5
fi

# not initialized -> prepare demo import file
if download_demo_db; then
  log "Demo DB placed at ${DEMO_DB_TARGET}. It will be imported during initialization."
else
  log "No demo DB prepared; the server will start empty."
fi

log "Starting official entrypoint"

# Pass control to the official entrypoint (preserve arguments)
exec "${OFFICIAL_ENTRYPOINT}" mysqld
