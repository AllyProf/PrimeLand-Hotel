<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sms = new \App\Services\SmsService();
echo "SMS PROVIDER DETAILS:\n";
echo "Base URL: https://messaging-service.co.tz\n";
echo "Balance Check:\n";
print_r($sms->getBalance());

echo "\nLOGS FOR LAST 5 MINS:\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logs = shell_exec('tail -n 100 ' . escapeshellarg($logFile));
    echo $logs;
}
