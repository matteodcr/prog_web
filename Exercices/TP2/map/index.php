<?php

include_once '../tp2-helpers.php';

define("API_ENDPOINT", "http://" . $_SERVER['HTTP_HOST'] . "/Exercices/TP2/comptage.php?");

function query_api(float $lat, float $lng, int $N) {
    return smartcurl(API_ENDPOINT . "top=$N&lat=$lat&lon=$lng", 0);
}

$markers = [];

if (isset($_GET["latitude"]) && isset($_GET["longitude"]) && isset($_GET["max"])) {
    $lat = floatval($_GET["latitude"]);
    $lng = floatval($_GET["longitude"]);
    $res = query_api($lat, $lng, floatval($_GET["max"]));
    $geo_json = json_decode($res, true);

    $markers[0] = [
        "name" => "Point sélectionné",
        "adr" => "$lat, $lng",
        "lat" => $lat,
        "lon" => $lng,
    ];

    $features = $geo_json["features"];
    foreach ($features as $feature) {
        assert($feature["geometry"]["type"] == "Point");
        $point = $feature["geometry"]["coordinates"];

        $markers[] = [
            "name" => $feature["properties"]["name"],
            "adr" => $feature["properties"]["antenne"],
            "lon" => $point[0],
            "lat" => $point[1],
        ];
    }
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TP2 — Carte</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
          integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
          crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
            integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
            crossorigin=""></script>
    <style>
        body {
            display: flex;
            flex-direction: row;
            align-items: center;
            margin: 0;

            font-family: sans-serif;
        }

        html, body, body > aside, body > main, #map {
            height: 100%;
        }

        body > main {
            flex-grow: 1;
        }

        body > aside {
            --overlap: 1em;
            margin-right: calc(-1 * var(--overlap));
            border-top-right-radius: var(--overlap);
            border-bottom-right-radius: var(--overlap);
            z-index: 999;

            width: 34%;
            max-width: 360px;
            background: #00B8FF;
            box-shadow: 0 0 10px black;
        }

        aside > form {
            padding: 1em;
        }

        fieldset {
            margin-top: 1em;
        }

        .leaflet-control-container { position: absolute; right: 56px }
    </style>
    <script>
        /** @type {{name: string, adr: string, lat: number, lon: number}[]} */
        const MARKERS = <?= json_encode($markers) ?>;
    </script>
    <script>
        // Icônes leaflet additionelles
        const greenIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
    </script>
</head>
<body>
<aside>
    <form>
        <h1>PW : TP2</h1>
        <p>
            <label for="latitude">Longitude</label>
            <input type="number" name="latitude" id="latitude" step="any">
            <br>
            <label for="longitude">Longitude</label>
            <input type="number" name="longitude" id="longitude" step="any">
            <br>
            <button type="button" id="picker">Sélectionner sur la carte</button>
        </p>

        <p>
            <label for="max">Nombres de points à afficher (N)</label>
            <input type="number" name="max" id="max" min="1" max="100" value="5">
        </p>

        <fieldset>
            <legend>Points d'accès Wi-Fi</legend>
            <input type="submit" value="Rechercher des points d'accès">
        </fieldset>

        <fieldset>
            <legend>Antennes GSM</legend>
            <label for="carrier">Opérateur</label>
            <select id="carrier" name="carrier">
                <option value="BYG">Bouygues Télécom</option>
                <option value="FREE">Free</option>
                <option value="ORA">Orange</option>
                <option value="SFR">SFR</option>
            </select>
            <input type="submit" value="Rechercher des antennes GSM">
        </fieldset>
    </form>
</aside>
<main>
    <div id="map"></div>
</main>
<script>
    const accessToken = 'pk.eyJ1IjoiYXBwZWxub25zdXJ0YXhlIiwiYSI6ImNrenBxc2d4YzAwczcydm85ZDlpOWY3MGYifQ.oDHNrN1KlCu_yNnxZ-W-hg';
    const form = document.querySelector('aside > form');
    const formLat = document.querySelector('aside > form [name=latitude]');
    const formLng = document.querySelector('aside > form [name=longitude]');
    const picker = document.querySelector('#picker');
    const map = L.map('map').setView([45.19, 5.72], 13);

    L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + accessToken, {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        maxZoom: 18,
        id: 'mapbox/streets-v11',
        tileSize: 512,
        zoomOffset: -1,
        accessToken: 'your.mapbox.access.token'
    }).addTo(map);

    let i = 0;
    for (const marker of MARKERS) {
        const options = (!(i++)) ? { icon: greenIcon } : undefined;

        marker.dom = L.marker([marker.lat, marker.lon], options)
            .bindPopup(`${marker.name}: ${marker.adr}`)
            .addTo(map)
            ._icon;
    }

    let picking = false;
    picker.addEventListener('click', () => {
        picking = !picking;
        updatePicker();
    });

    map.on('click', ({ latlng }) => {
        if (picking) {
            picking = false;
            updatePicker();
            formLat.value = latlng.lat;
            formLng.value = latlng.lng;
        }
    });

    function updatePicker() {
        document.querySelector('#map').style.cursor = picking ? 'crosshair' : '';
        form.style.opacity = picking ? 0.5 : 1;
    }
</script>
</body>
</html>