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
    console.error('[run-build] Step 1/2: composer install…');
    const { composerInstall } = await import('./vercel-install.mjs');
    await composerInstall();
    console.error('[run-build] Step 2/2: vite build…');
  }
  execSync('vite build', { stdio: 'inherit', cwd: root, env: process.env });
}

main().catch((err) => {
  console.error('[run-build] FAILED:', err?.message ?? err);
  process.exit(1);
});
