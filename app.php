<?php

require_once 'CurrencyCommisions.php';

$app = new CurrencyCommisions();

$file = @file_get_contents($argv[1]);

if (!$file) {
    echo 'Wrong input data';
} else {
    $app->run($file);
}
