<?php

/**
 * Vercel serverless entry — forwards to Laravel's front controller.
 */

if (!is_dir('/tmp/views')) {
    mkdir('/tmp/views', 0755, true);
}

require __DIR__.'/../public/index.php';
