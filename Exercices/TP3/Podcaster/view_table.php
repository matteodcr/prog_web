<?php
// Pour que mon IDE ne se plaigne pas
/** @var array $episodes */
?>

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
        <tr style="background: lightgray">
            <td class="divider" colspan="5"><span>Semaine <?= $episode->week ?> / <?= $episode->year ?></span></td>
        </tr>
    <?php } ?>
    <tr>
        <th>
            <a href="<?= htmlspecialchars($episode->url) ?>" target="_blank" title="<?= htmlspecialchars($episode->description) ?>">
                <?= htmlspecialchars($episode->title) ?>
            </a>
        </th>
        <td><?= $episode->parisDate ?></td>
        <td><audio controls src="<?= htmlspecialchars($episode->mediaUrl) ?>"></audio></td>
        <td><?= parseDuration($episode->description) ?></td>
        <td>
            <a href="<?= htmlspecialchars($episode->mediaUrl) ?>" target="_blank" download="<?= htmlspecialchars($episode->title) ?>.mp3">[télécharger]</a>
        </td>
    </tr>
    <?php $last = $episode; } ?>
</tbody>
