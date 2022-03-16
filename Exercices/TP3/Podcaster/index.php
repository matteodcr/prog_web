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
            text-align: center;
            vertical-align: middle;
        }

        .divider {
            padding-top: 1em;
            font-weight: bolder;
            font-style: italic;
        }
    </style>
</head>
<body class="doodle">
<h1><a href="<?= $feed->link ?>" target="_blank" title="<?= $feed->description ?>"><?= $feed->title ?></a></h1>
<table>
    <caption>Podcasts <i><?= $feed->title ?></i></caption>
    <thead>
        <tr>
            <th>Titre</th>
            <th>Date</th>
            <th>Lecteur</th>
            <th>Durée</th>
            <th>Media</th>
        </tr>
    </thead>
    <tbody>
    <?php $last = null; foreach ($episodes as $episode) { ?>
        <?php if ($last == null || $last->week !== $episode->week) { ?>
        <tr>
            <td class="divider" colspan="5"><span>Semaine <?= $episode->week ?> / <?= $episode->year ?></span></td>
        </tr>
        <?php } ?>
        <tr>
            <th>
                <a href="<?= $episode->link ?>" target="_blank" title="<?= $episode->description ?>"><?= $episode->title ?></a>
            </th>
            <td><?= $episode->parisDate ?></td>
            <td><audio controls src="<?= $episode->mediaUrl ?>"></audio></td>
            <td><?= parseDuration($episode->description) ?></td>
            <td>
                <a href="<?= $episode->mediaUrl ?>" target="_blank" download="<?= $episode->title ?>.mp3">[télécharger]</a>
            </td>
        </tr>
    <?php $last = $episode; } ?>
    </tbody>
</table>
</body>
</html>
