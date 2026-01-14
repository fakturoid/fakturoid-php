<?php

$envFile = __DIR__ . '/../.env';

if (file_exists($envFile)) {
    $dotenv = new \Symfony\Component\Dotenv\Dotenv();
    $dotenv->usePutenv(true);
    $dotenv->load($envFile);
    echo "✓ Loaded .env file\n";
} else {
    echo "⚠ Warning: .env file not found at: $envFile\n";
    echo "Please copy .env.example to .env and configure your credentials\n";
}
