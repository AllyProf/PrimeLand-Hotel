<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sms = new \App\Services\SmsService();
$balance = $sms->getBalance();
print_r($balance);
