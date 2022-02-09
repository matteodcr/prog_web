<?php

include_once 'utils.php';

const MONTH_NAMES = array(null, "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
const DAYS_NAMES = array("Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche");

define("YEAR", $_GET["year"] ?? intval(date('Y')));
define("MONTH", $_GET["month"] ?? intval(date('m')));
define("ENABLE_COLUMN_VIEW", isset($_GET['column-view']));

if (MONTH < 1 || MONTH > 12) {
    die("mois invalide: " . MONTH);
}

if (YEAR < 1971 || YEAR > 2037) {
    die("année invalide: " . YEAR . " ∉ [1971; 2037]");
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Agenda</title>
    <style>
        body, html {
            margin: 0;
            width: 100%;
            height: 100%;
            font-family: sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        thead tr {
            background-color: orange;
        }

        thead tr th {
            font-size: 1.4em;
            padding: 0.8em;
        }

        table td {
            border: 1px solid gray;
        }

        tbody td {
            padding: 1em;
        }
    </style>
</head>
<body>
    <?php
    if (!ENABLE_COLUMN_VIEW) include 'vue_mois.php';
    else include 'vue_colonne.php';
    ?>
</body>
</html>
