/**
 * Installs PHP via OS package manager if needed, then composer install --no-dev.
 */
import { execSync } from 'node:child_process';
import { randomBytes } from 'node:crypto';
import { createWriteStream } from 'node:fs';
import { chmodSync, existsSync } from 'node:fs';
import { get } from 'node:https';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const composerPhar = join('/tmp', 'composer.phar');

function hasPhp() {
  try {
    execSync('php -v', { stdio: 'pipe', env: process.env });
    return true;
  } catch {
    return false;
  }
}

function sh(cmd, quiet = false) {
  try {
    execSync(cmd, {
      stdio: quiet ? 'pipe' : 'inherit',
      env: process.env,
      shell: '/bin/bash',
    });
    return true;
  } catch {
    return false;
  }
}

function installPhp() {
  if (hasPhp()) return;

  // intl/zip/tokenizer often required by Laravel + Composer scripts (package:discover).
  const attempts = [
    'dnf install -y php php-cli php-common php-mbstring php-xml php-pdo php-mysqlnd php-intl php-json php-bcmath php-process php-zip php-openssl php-curl',
    'dnf install -y php-cli php-mbstring php-xml php-pdo php-mysqlnd php-intl php-zip',
    'dnf install -y php php-cli php-common php-mbstring php-xml php-pdo php-mysqlnd',
    'dnf install -y php-cli php-mbstring php-xml php-pdo php-mysqlnd',
    'dnf install -y php8.3',
    'microdnf install -y php-cli php-mbstring php-xml php-pdo php-mysqlnd',
    'yum install -y php-cli php-mbstring php-xml php-pdo php-mysqlnd',
    'apt-get update -y && apt-get install -y php-cli php-mbstring php-xml php-mysql',
    'apk add --no-cache php83 php83-phar php83-mbstring php83-xml php83-pdo php83-mysqlnd php83-openssl php83-curl',
    'sudo dnf install -y php php-cli php-mbstring php-xml php-pdo php-mysqlnd',
    'sudo apt-get update -y && sudo apt-get install -y php-cli php-mbstring php-xml php-mysql',
  ];
  for (let i = 0; i < attempts.length; i++) {
    const quiet = i < attempts.length - 1;
    sh(attempts[i], quiet);
    if (hasPhp()) return;
  }
}

function downloadFile(url, dest) {
  return new Promise((resolve, reject) => {
    get(url, (res) => {
      if (res.statusCode === 301 || res.statusCode === 302) {
        const loc = res.headers.location;
        if (loc) return downloadFile(loc, dest).then(resolve).catch(reject);
      }
      if (res.statusCode !== 200) {
        reject(new Error(`GET ${url} -> ${res.statusCode}`));
        return;
      }
      const f = createWriteStream(dest);
      res.pipe(f);
      f.on('finish', () => f.close(() => resolve()));
      f.on('error', reject);
    }).on('error', reject);
  });
}

export async function composerInstall() {
  installPhp();

  if (!hasPhp()) {
    console.error(`
[vercel-install] PHP not found — dnf/apt could not install it.

FIX (choose one):
  A) Vercel → Project → Settings → General → Build & Development Settings
     → Build Command: set to exactly:  npm run build
     → Turn OFF override if it still shows: curl ... | php ...
     (Dashboard overrides vercel.json — your logs prove override is ON.)

  B) Deploy this Laravel app on Railway (PHP buildpack) — works with your Railway DB.

`);
    process.exit(127);
  }

  if (!existsSync(composerPhar)) {
    await downloadFile('https://getcomposer.org/download/latest-stable/composer.phar', composerPhar);
    try {
      chmodSync(composerPhar, 0o755);
    } catch {
      /* ignore */
    }
  }

  const buildEnv = {
    ...process.env,
    APP_ENV: process.env.APP_ENV ?? 'production',
    APP_DEBUG: 'false',
    LOG_CHANNEL: process.env.LOG_CHANNEL ?? 'stderr',
    APP_KEY:
      process.env.APP_KEY ??
      `base64:${randomBytes(32).toString('base64')}`,
    CI: process.env.CI ?? 'true',
    COMPOSER_ALLOW_SUPERUSER: '1',
  };

  execSync(`php ${composerPhar} install --no-dev --optimize-autoloader --no-interaction`, {
    stdio: 'inherit',
    cwd: root,
    env: buildEnv,
  });
}
