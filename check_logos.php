<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BusinessCard;

$cards = BusinessCard::select('id', 'name', 'logo')->get();

foreach ($cards as $card) {
    echo "ID: {$card->id}, Name: {$card->name}, Logo: {$card->logo}\n";
} 