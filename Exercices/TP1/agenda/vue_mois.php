<?php
$days_of_month = iter_month_days(YEAR, MONTH);
;

function create_week(): array {
    return ["", "", "", "", "", "", ""];
}

$rows = [create_week()];

foreach ($days_of_month as $day) {
    $dow = $day["day_of_week"];
    $rows[array_key_last($rows)][$dow] = $day["day_of_month"];
    if ($dow == 6) {
        $rows[] = create_week();
    }
}
?>

<table>
    <thead>
        <?php include 'composant_entete.php' ?>
        <tr>
            <th>Lundi</th>
            <th>Mardi</th>
            <th>Mercredi</th>
            <th>Jeudi</th>
            <th>Vendredi</th>
            <th>Samedi</th>
            <th>Dimanche</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($rows as $row) {
            if ($row === create_week()) break;

            echo '<tr>';

            foreach ($row as $day) {
                echo "<td>$day</td>";
            }

            echo '</tr>';
        }
        ?>
    </tbody>
</table>
