<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Résultat</title>
</head>
<body>
    <?php
    require_once 'libcalcul.php';

    $sum = floatval($_GET['sum']);
    $rate = floatval($_GET['rate']);
    $duration = floatval($_GET['duration']);

    $total = cumul($sum, $rate, $duration);

    echo '<b>' . $total . '</b>';
    ?>
</body>
</html>