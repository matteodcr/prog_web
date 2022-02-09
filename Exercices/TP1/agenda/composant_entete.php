<?php

$month_name = MONTH_NAMES[MONTH];

$month_prev = (MONTH - 2 + 12) % 12 + 1;
$month_next = (MONTH) % 12 + 1;
$year_prev = YEAR - ($month_prev == 12 ? 1 : 0);
$year_next = YEAR - ($month_next == 1 ? 1 : 0);

$table_width = ENABLE_COLUMN_VIEW ? 3 : 7;

$cv_param = ENABLE_COLUMN_VIEW ? '&column-view' : '';

$year = YEAR;
$month = MONTH;

$cv_param_inv = !ENABLE_COLUMN_VIEW ? '&column-view' : '';
/** URL pour inverser le mode d'affichage actuel: mode colonne/mode calendrier */
$other_mode_url = "?year=$year&month=$month$cv_param_inv"

?>

<tr>
    <th colspan="<?= $table_width ?>">
        <div>
            <a href="<?= "?year=$year_prev&month=$month_prev$cv_param" ?>">&lt; <?= MONTH_NAMES[$month_prev] ?></a>
            <?= $month_name ?>
            <a href="<?= "?year=$year_next&month=$month_next$cv_param" ?>"><?= MONTH_NAMES[$month_next] ?> &gt;</a>
        </div>
        <div>
            <label>
                <input
                    type="checkbox"
                    <?= ENABLE_COLUMN_VIEW ? '' : 'checked' ?>
                    onchange="this.disabled = true; location.href = `<?= $other_mode_url ?>`;"
                >
                Mode calendrier
            </label>
        </div>
    </th>
</tr>
