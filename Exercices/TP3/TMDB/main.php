<?php

include_once '../../../Helpers/tp3-helpers.php';

// On construit l'url pour récupérer le fichier JSON du film avec l'API de TMDB
// On utilise GET, l'id du film est saisie sur le formulaire dans formulaire.php
$url = "https://api.themoviedb.org/3/movie/" . $_GET["film"] . "?api_key=ebb02613ce5a2ae58fde00f4db95a9c1";

// On récupère le fichier json avec la fonction fournie dans helper
$film_json = smartcurl($url)[0];

// On transforme le fichier JSON en tableau associatif
$film_array = json_decode($film_json, true);
?>

<!DOCTYPE HTML>

<html lang="fr">

<head>
    <meta charset="utf-8"/>
    <title>Infos?></title>
</head>

<body>

<?php
// On affiche chaque champ voulu un à un.
echo "<p> ID : " . $film_array["id"] . "</p><br />";
echo "<p> TITLE : " . $film_array["title"] . "</p><br />";
echo "<p> ORIGINAL TITLE : " . $film_array["original_title"] . "</p><br />";
if (isset($film_array["tagline"]) && strlen($film_array["tagline"]) > 0) {
    echo "<p> TAGLINE: " . $film_array["tagline"] . "</p><br />";
}
echo "<p> OVERVIEW : " . $film_array["overview"] . "</p><br />";

$movie_url = "https://www.themoviedb.org/movie/" . $film_array['id'];
echo "<p> <a href=\"" . $movie_url . "\"" . " </a>" . $film_array["title"] . "</p>";
?>

</body>

</html>