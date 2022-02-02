<?php

function cumul(float $sum, float $rate, float $duration): float {
    return $sum * pow(1 + $rate / 100, $duration);
}
