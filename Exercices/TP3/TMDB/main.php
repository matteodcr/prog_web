<?php

include_once 'tp3-helpers.php';

echo smartcurl("https://api.themoviedb.org/3/movie/550?api_key=ebb02613ce5a2ae58fde00f4db95a9c1")[0] . "\n";