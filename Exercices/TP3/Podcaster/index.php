<?php

require_once 'vendor/dg/rss-php/src/Feed.php';
require_once 'episode.php';

const FEED_URL = 'http://radiofrance-podcast.net/podcast09/rss_14312.xml';
try {
    $feed = Feed::loadRss(FEED_URL);
} catch (FeedException $e) {
    die($e);
}

$episodes = [];
foreach ($feed->item as $item) {
    $episodes[] = new Episode($item);
}

// Episodes triés chronologiquement par semaine
// Les indices sont des identifiants générés à partir de l'année et du numéro de semaine, qui conservent l'ordre chrono.
$episodes_by_week = [];

foreach ($episodes as $episode) {
    // Un indice unique à chaque semaine
    $idx = $episode->year * 52 + $episode->week;

    if ($episode->weekday >= 5) {
        die("Le jour de semaine d'un épisode ne peut pas tomber un week-end");
    }

    // Si la semaine n'existe pas encore, on l'initialise
    if (!isset($episodes_by_week[$idx])) {
        $episodes_by_week[$idx] = array_fill(0, 5, []);
    }

    // L'indice "$episode->date->getTimestamp()" permet de trier les épisodes au sein d'une case
    $episodes_by_week[$idx][$episode->weekday][$episode->date->getTimestamp()] = $episode;
    // Ce n'est pas très performant de faire ça à chaque fois, mais le plus simple ici
    ksort($episodes_by_week[$idx][$episode->weekday]);
}

krsort($episodes_by_week);

?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://chr15m.github.io/DoodleCSS/doodle.css">
    <title>Podcaster</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Short+Stack&display=swap');

        body {
            font-family: 'Short Stack', cursive;
        }
    </style>
    <style>
        td {
            padding: 1em;
            text-align: center;
            vertical-align: middle;
        }

        tr:nth-child(even) td:nth-child(odd), tr:nth-child(odd) td:nth-child(even) {
            background-color: rgba(0, 0, 0, 0.1);
        }

        .podcast {
            display: block;
        }

        .podcast:not(:last-child) {
            margin-bottom: 1em;
            border-bottom: 1px dashed gray;
            padding-bottom: 1em;
        }
    </style>
</head>
<body class="doodle">
<h1><a href="<?= $feed->link ?>" target="_blank" title="<?= $feed->description ?>"><?= $feed->title ?></a></h1>
<table>
    <caption>Podcasts <i><?= $feed->title ?></i></caption>
    <thead>
        <tr>
            <th>Lundi</th>
            <th>Mardi</th>
            <th>Mercredi</th>
            <th>Jeudi</th>
            <th>Vendredi</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($episodes_by_week as $week) { ?>
        <tr>
            <?php foreach ($week as $day) { ?>
                <td>
                    <?php foreach ($day as $episode) { ?>
                        <div class="podcast">
                            <a href="<?= htmlspecialchars($episode->url) ?>" target="_blank" title="<?= htmlspecialchars($episode->description) ?>"><?= htmlspecialchars($episode->title) ?></a>
                            <span>(<?= $episode->parisDate ?>)</span>
                            <audio controls src="<?= htmlspecialchars($episode->mediaUrl) ?>"></audio>
                        </div>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>
    <?php } ?>
    </tbody>
</table>
</body>
</html>
