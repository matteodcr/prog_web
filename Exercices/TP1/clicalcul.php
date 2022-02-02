<?php

require_once 'libcalcul.php';

$sum = floatval($argv[1]);
$rate = floatval($argv[2]);
$duration = floatval($argv[3]);

echo cumul($sum, $rate, $duration) . '\n';
