<?php

require_once('vendor/dg/rss-php/src/Feed.php');

const FEED_URL = 'http://radiofrance-podcast.net/podcast09/rss_14312.xml';
try {
    $feed = Feed::loadRss(FEED_URL);
} catch (FeedException $e) {
    die($e);
}

/**
 * Formatte une date UTC au fuseau horraire de Paris
 */
function formatDate(int $timestamp) {
    $date = DateTime::createFromFormat('U', $timestamp, new DateTimeZone('UTC'));
    $date->setTimeZone(new DateTimeZone('Europe/Paris'));
    return $date->format('d/m/y G:i');
}

/**
 * @param string $description Example: "durée : 00:05:48 - Le Journal des sciences - par : Natacha Triou - La..."
 * @return string "00:05:48"
 */
function parseDuration(string $description) {
    $matches = [];
    if (preg_match('/\d{2}:\d{2}:\d{2}/', $description, $matches) == 1) {
        return $matches[0];
    } else {
        return '<i>durée inconnue</i>';
    }
}

function getMediaUrl($item) {
    return $item->enclosure->attributes()->url;
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
    <?php foreach ($feed->item as $item) { ?>
        <tr>
            <th>
                <a href="<?= $item->link ?>" target="_blank" title="<?= $item->description ?>"><?= $item->title ?></a>
            </th>
            <td>
                <?= formatDate(intval($item->timestamp)) ?>
            </td>
            <td>
                <audio controls src="<?= getMediaUrl($item) ?>"></audio>
            </td>
            <td><?= parseDuration($item->description) ?></td>
            <td>
                <a href="<?= getMediaUrl($item) ?>" target="_blank" download="<?= $item->title ?>.mp3">[télécharger]</a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
</body>
</html>
