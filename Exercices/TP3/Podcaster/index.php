<?php

require_once 'vendor/dg/rss-php/src/Feed.php';
require_once 'episode.php';

$view_as_table = false;
if (isset($_GET["view"])) {
    switch ($_GET["view"]) {
        case "weeks":
            break;
        case "table":
            $view_as_table = true;
            break;
        default:
            die("Unknown view type: " . $_GET["view"]);
    }
}

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
    <!--suppress CssUnusedSymbol -->
    <style>
        td {
            text-align: center;
            vertical-align: middle;
        }

        td.day {
            padding: 1em;
        }

        .checkerboard tr:nth-child(even) td:nth-child(odd), .checkerboard tr:nth-child(odd) td:nth-child(even) {
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
<a href="?view=table">vue table</a> • <a href="?view=weeks">vue semaines</a>
<h1><a href="<?= $feed->link ?>" target="_blank" title="<?= $feed->description ?>"><?= $feed->title ?></a></h1>
<table>
    <caption>Podcasts <i><?= $feed->title ?></i></caption>
    <?php $view_as_table ? include 'view_table.php' : include 'view_calendar.php' ?>
</table>
</body>
</html>
