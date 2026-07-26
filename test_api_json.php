<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(Illuminate\Http\Request::create('/docs/api.json', 'GET'));
echo substr($response->getContent(), 0, 100);
