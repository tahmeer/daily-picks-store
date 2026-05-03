/**
 * Local: only `vite build`.
 * Vercel (VERCEL=1): PHP/composer then vite — works when Dashboard Build Command is `npm run build`.
 */
import { execSync } from 'node:child_process';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');

async function main() {
  if (process.env.VERCEL === '1') {
    const { composerInstall } = await import('./vercel-install.mjs');
    await composerInstall();
  }
  execSync('vite build', { stdio: 'inherit', cwd: root, env: process.env });
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
