<?php

$lines = file('http://entrepot.metropolegrenoble.fr/opendata/wifi/csv/borneswifi_EPSG4326.csv');
echo count($lines)-1; // on enleve l'en-tête

