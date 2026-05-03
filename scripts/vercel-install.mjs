/**
 * Installs PHP via OS package manager if needed, then composer install --no-dev.
 */
import { execSync } from 'node:child_process';
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

function sh(cmd) {
  try {
    execSync(cmd, { stdio: 'inherit', env: process.env, shell: '/bin/bash' });
    return true;
  } catch {
    return false;
  }
}

function installPhp() {
  if (hasPhp()) return;

  const attempts = [
    'dnf install -y php-cli php-mbstring php-xml php-pdo php-mysqlnd',
    'dnf install -y php8.3 php8.3-cli php8.3-mbstring php8.3-xml php8.3-pdo php8.3-mysqlnd',
    'microdnf install -y php-cli php-mbstring php-xml php-pdo php-mysqlnd',
    'yum install -y php-cli php-mbstring php-xml php-pdo php-mysqlnd',
    'apt-get update -y && apt-get install -y php-cli php-mbstring php-xml php-mysql',
    'apk add --no-cache php83 php83-phar php83-mbstring php83-xml php83-pdo php83-mysqlnd php83-openssl php83-curl',
    'sudo dnf install -y php-cli php-mbstring php-xml php-pdo php-mysqlnd',
    'sudo apt-get update -y && sudo apt-get install -y php-cli php-mbstring php-xml php-mysql',
  ];
  for (const cmd of attempts) {
    sh(cmd);
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

  execSync(`php ${composerPhar} install --no-dev --optimize-autoloader --no-interaction`, {
    stdio: 'inherit',
    cwd: root,
    env: process.env,
  });
}
