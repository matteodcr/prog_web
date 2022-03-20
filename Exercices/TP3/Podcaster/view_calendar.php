<?php
// Pour que mon IDE ne se plaigne pas
/** @var array $episodes */

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

<thead>
<tr>
    <th>Lundi</th>
    <th>Mardi</th>
    <th>Mercredi</th>
    <th>Jeudi</th>
    <th>Vendredi</th>
</tr>
</thead>

<tbody class="checkerboard">
<?php foreach ($episodes_by_week as $week) { ?>
    <tr>
        <?php foreach ($week as $day) { ?>
            <td class="day">
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
