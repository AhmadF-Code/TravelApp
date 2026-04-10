<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::create('/trip/bali-magic', 'GET'));
echo $response->getStatusCode() . "\n";
echo substr($response->getContent(), 0, 2000);
