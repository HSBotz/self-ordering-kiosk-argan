<?php

// Untuk Vercel: Atur variabel lingkungan dari vercel.json
if (isset($_ENV['VERCEL_REGION'])) {
    $_ENV['APP_ENV'] = 'production';
    $_ENV['APP_DEBUG'] = 'false';
    $_ENV['APP_URL'] = 'https://kedai-coffee-kiosk.vercel.app';
    $_ENV['APP_CONFIG_CACHE'] = '/tmp/config.php';
    $_ENV['APP_EVENTS_CACHE'] = '/tmp/events.php';
    $_ENV['APP_PACKAGES_CACHE'] = '/tmp/packages.php';
    $_ENV['APP_ROUTES_CACHE'] = '/tmp/routes.php';
    $_ENV['APP_SERVICES_CACHE'] = '/tmp/services.php';
    $_ENV['VIEW_COMPILED_PATH'] = '/tmp';
    $_ENV['CACHE_DRIVER'] = 'array';
    $_ENV['LOG_CHANNEL'] = 'stderr';
    $_ENV['SESSION_DRIVER'] = 'cookie';
}

// Arahkan ke public/index.php
require __DIR__ . '/../public/index.php'; 