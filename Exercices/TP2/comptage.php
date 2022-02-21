<?php
include_once 'tp2-helpers.php';

session_start();

function csv_extract(string $filename)
{
    $csv = array_map('str_getcsv', file('borneswifi.csv'));
    array_walk($csv, function (&$a) use ($csv) {
        $a = array_combine($csv[0], $a); // clé = premiere ligne, valeur = ligne en coirs
    });
    array_shift($csv); # remove column header
    return $csv;
}

function print_csv(array $csv)
{
    echo "nombre antennes : " . count($csv) . "\n";
    foreach ($csv as $line) {
        echo "\n";
        foreach ($line as $key => $value) {
            echo "$key=>$value\n";
        }
    }
}

function proche(array $csv, array $d1)
{
    $tab = [];
    foreach ($csv as $line) {
        $d2['lon'] = floatval($line['lon']);
        $d2['lat'] = floatval($line['lat']);
        $d['name'] = $line['name'];
        $d['distance'] = distance($d1, $d2);
        array_push($tab, $d);
    }

    $name = array_column($tab, 'name');
    $distance = array_column($tab, 'distance');
    array_multisort($distance, $name);

    return $name;
}

function n_premiers(array $tab, int $N)
{
    $n_array = [];
    for ($i = 0; $i < $N; $i++) {
        array_push($n_array, $tab[$i]);
    }
    return $n_array;
}

function geocodage_inverse(array $csv)
{
    foreach ($csv as &$line) {
        $url = "https://api-adresse.data.gouv.fr/reverse/?lon=" . $line["lon"] . "&lat=" . $line["lat"];

        // On met en cache la réponse comme ça peut prendre du temps
        if (!isset($_SESSION[$url])) {
            $res = smartcurl($url, 0);
            $_SESSION[$url] = $url;
        } else {
            $res = $_SESSION[$url];
        }

        $json = json_decode($res, true);
        $street = $json["features"]["0"]["properties"]["label"];
        $line["adresse"] = $street;
    }
    return $csv;
}

function conversion_cell_json(array $cell)
{
    return [
        "type" => "Feature",
        "geometry" => [
            "type" => "Point",
            "coordinates" => [floatval($cell['lon']), floatval($cell['lat'])],
        ],
        "properties" => [
            "name" => $cell['name'],
            "adresse" => $cell['adresse'],
            "antenne" => $cell['ant'],
        ],
    ];
}

function conversion_json(array $csv)
{
    $main_array = [];
    foreach ($csv as &$line) {
        array_push($main_array, conversion_cell_json($line));
    }
    $head = ["type" => "FeatureCollection", "features" => $main_array];
    return $head;
}

$filename = 'borneswifi.csv';
$csv = csv_extract($filename);
$csv = geocodage_inverse($csv);

// Q8
$top = floatval($_GET['top']);
$lon = floatval($_GET['lon']);
$lat = floatval($_GET['lat']);


$geopoint = geopoint($lon, $lat);
$proche = proche($csv, $geopoint);
$array_n = n_premiers($proche, $top);

$n_csv = [];
foreach ($csv as &$line) {
    foreach ($array_n as $name) {
        if ($name == $line["name"]) {
            array_push($n_csv, $line);
        }
    }
}

header("Content-Type: application/json");
echo json_encode(conversion_json($n_csv));

