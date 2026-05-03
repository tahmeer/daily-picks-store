#!/usr/bin/env bash
# Vercel Node build image often has no `php` in PATH — install CLI or fail clearly.
set -euo pipefail

download() {
  if command -v curl >/dev/null 2>&1; then
    curl -fsSL "$1"
  elif command -v wget >/dev/null 2>&1; then
    wget -qO- "$1"
  else
    echo 'ERROR: need curl or wget' >&2
    exit 127
  fi
}

ensure_php() {
  if command -v php >/dev/null 2>&1; then
    return 0
  fi
  for p in /usr/bin/php /usr/local/bin/php; do
    if [[ -x "$p" ]]; then
      export PATH="$(dirname "$p"):$PATH"
      return 0
    fi
  done

  echo 'PHP not in PATH — trying OS packages (needs sudo on this image)...' >&2
  if command -v sudo >/dev/null 2>&1 && command -v dnf >/dev/null 2>&1; then
    sudo dnf install -y php-cli php-mbstring php-xml php-pdo php-mysqlnd || true
  fi
  if command -v sudo >/dev/null 2>&1 && command -v apt-get >/dev/null 2>&1; then
    sudo DEBIAN_FRONTEND=noninteractive apt-get update -y
    sudo DEBIAN_FRONTEND=noninteractive apt-get install -y php-cli php-mbstring php-xml php-mysql || true
  fi

  if command -v php >/dev/null 2>&1; then
    return 0
  fi
  echo 'ERROR: php could not be installed. Use Vercel dashboard → change build image or deploy Laravel on Railway/Render.' >&2
  exit 127
}

ensure_php

download 'https://getcomposer.org/installer' | php -- --install-dir=/tmp --filename=composer
php /tmp/composer install --no-dev --optimize-autoloader --no-interaction
npm run build
