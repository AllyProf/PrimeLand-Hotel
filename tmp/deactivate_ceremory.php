<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\DB::table('service_catalog')->where('service_name', 'Ceremory')->update(['is_active' => 0]);
echo "Deactivated Ceremory\n";
