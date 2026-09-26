#!/usr/bin/env bash
# macOS dev database launcher (Apple Container, Docker-compatible CLI).
# Windows equivalent: db.debug.ps1. Creates the bookshelf DB on first boot
# via MYSQL_DATABASE.
# Usage: ./db.debug.mac.sh   (stop with: container stop bookshelf-sql)
set -euo pipefail

IMAGE="mariadb:10.11"
NAME="bookshelf-sql"
VOLUME="bookshelf-mysql"

if container inspect "$NAME" >/dev/null 2>&1; then
    echo "$NAME already exists; starting it."
    container start "$NAME"
    exit 0
fi

exec container run --rm -it \
    --name "$NAME" \
    -e MYSQL_ROOT_PASSWORD=password \
    -e MYSQL_DATABASE=bookshelf \
    -e MYSQL_USER=app \
    -e "MYSQL_PASSWORD=!ChangeMe!" \
    -v "$VOLUME:/var/lib/mysql" \
    -p 3306:3306 \
    "$IMAGE"
