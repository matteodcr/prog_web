<?php
$days_of_month = iter_month_days(YEAR, MONTH);
?>

<table>
    <thead>
        <?php include 'composant_entete.php' ?>
        <tr>
            <th>#</th>
            <th>Jour de la semaine</th>
            <th>Événements</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($days_of_month as $day) {
            $dow = DAYS_NAMES[$day["day_of_week"]];
            $dom = $day["day_of_month"];
            echo "<tr><td>$dom</td><td>$dow</td><td></td></tr>";
        }
        ?>
    </tbody>
</table>
