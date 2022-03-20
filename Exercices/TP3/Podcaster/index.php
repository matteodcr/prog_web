<?php

// Configuration des flux à syndiquer
// Pour la vue semaine, il est impératif qu'il n'y ait pas d'épisodes le week-end, j'ai décidé de faire `die` le script
// plutôt que d'ignorer l'épisode.
const FEED_URLS = [
    'https://radiofrance-podcast.net/podcast09/rss_14312.xml',
    'https://radiofrance-podcast.net/podcast09/rss_10076.xml',
];

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

$feeds = [];
foreach (FEED_URLS as $feed_url) {
    try {
        $feeds[$feed_url] = Feed::loadRss($feed_url);
    } catch (FeedException $e) {
        die($e);
    }
}

$multipleFeeds = count($feeds) > 1;

$episodes = [];
foreach ($feeds as $feed) {
    foreach ($feed->item as $item) {
        $episode = new Episode($item, $multipleFeeds ? $feed->title : null);
        $episodes[$episode->date->getTimestamp()] = $episode;
    }
}

krsort($episodes);

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

<?php if (!$multipleFeeds) { ?>
    <h1>
        <a href="<?= htmlspecialchars($feed->link) ?>" target="_blank" title="<?= htmlspecialchars($feed->description) ?>">
            <?= htmlspecialchars($feed->title) ?>
        </a>
    </h1>
<?php } else { ?>
    <h1><i><?= count($feeds) ?> flux combinés</i></h1>
    <ul>
        <?php foreach ($feeds as $feed) { ?>
            <li>
                <a href="<?= htmlspecialchars($feed->link) ?>" target="_blank" title="<?= htmlspecialchars($feed->description) ?>">
                    <?= htmlspecialchars($feed->title) ?>
                </a>
            </li>
        <?php } ?>
    </ul>
<?php } ?>
<i><b>note:</b> le·s flux peut·vent être modifié·s grâce à la constante <code>FEED_URLS</code> de <code>index.php</code></i>

<table>
    <caption>Podcasts</caption>
    <?php $view_as_table ? include 'view_table.php' : include 'view_calendar.php' ?>
</table>
</body>
</html>
