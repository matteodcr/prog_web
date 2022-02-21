<?php
include_once 'tp2-helpers.php';

function csv_extract($filename)
{
    $csv = array_map('str_getcsv', file('borneswifi.csv'));
    array_walk($csv, function (&$a) use ($csv) {
        $a = array_combine($csv[0], $a); // clé = premiere ligne, valeur = ligne en coirs
    });
    array_shift($csv); # remove column header
    return $csv;
}

function print_csv($csv)
{
    echo "nombre antennes : " . count($csv) . "\n";
    foreach ($csv as $line) {
        echo "\n";
        foreach ($line as $key => $value) {
            echo "$key=>$value\n";
        }
    }
}

function proche($csv, array $d1)
{
    $tab = [];
    echo "Voici les antennes à moins de 200m\n";
    foreach ($csv as $line) {
        $d2['lon'] = floatval($line['lon']);
        $d2['lat'] = floatval($line['lat']);
        $d['name'] = $line['name'];
        $d['distance'] = distance($d1, $d2);
        if ($d['distance'] < 200) {
            echo $d['name'];
            echo "\n";
        }
        array_push($tab, $d);
    }

    $name = array_column($tab, 'name');
    $distance = array_column($tab, 'distance');

// Sort the data with volume descending, edition ascending
// Add $data as the last parameter, to sort by the common key
    array_multisort($distance, $name);

    echo "Voici les antennes les plus proches par ordre croissant\n";
    foreach ($name as $line) {
        echo "$line\n";
    }

    return $name;
}

function n_premiers($tab, $N)
{
    echo "Voici les $N plus proches antennes \n";
    for ($i = 0; $i < $N; $i++) {
        echo $tab[$i];
        echo "\n";
    }
}

function geocodage_inverse($csv)
{
    foreach ($csv as &$line) {
        $url = "https://api-adresse.data.gouv.fr/reverse/?lon=" . $line["lon"] . "&lat=" . $line["lat"];
        $json = json_decode(smartcurl($url, 0), true);
        $street = $json["features"]["0"]["properties"]["label"];
        $line["adresse"] = $street;
    }
    return $csv;
}

$filename = 'borneswifi.csv';
$d1['lon'] = 5.72752;
$d1['lat'] = 45.19102;
if (isset($argv[1])) {
    $N = intval($argv[1]);
} else $N = 5;


$csv = csv_extract($filename);
print_csv($csv);

// Q5
$tab = proche($csv, $d1);

// Q6
n_premiers($tab, $N);

// Q7
print_csv(geocodage_inverse($csv));

