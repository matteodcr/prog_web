<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Table de multiplication</title>
    <style>
        .container {
            border-radius: 0.4em
        }

        table {
            border-collapse: collapse;
            font-family: sans-serif;
        }

        td, th {
            padding: 0.4em;
            border: 1px solid black;
            text-align: center;
        }

        tr th {
            background-color: black;
            color: white;
            border-color: white;
            font-weight: bold;
        }

        tr:hover {
            background: rgba(255, 255, 0, 0.5);
        }

        .selected {
            background: yellow !important;
        }

        form {
            margin-top: 2em;
            padding: 0.2em;
            border: 2px solid black;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        $cols = 10;
        $rows = 10;

        if (isset($_GET['cols'])) { $cols = intval($_GET['cols']); }
        if (isset($_GET['rows'])) { $rows = intval($_GET['rows']); }
        $selected = intval($_GET['selected'] ?: -1);

        if ($cols < 1 || $rows < 1) die('Invalid parameters');
        ?>
        <table>
            <thead>
            <tr>
                <th>*</th>
                <?php
                for ($i = 1; $i <= $cols; $i++) {
                    echo "<th>$i</th>";
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php
            for ($row = 1; $row <= $rows; $row++) {
                if ($selected == $row) {
                    echo '<tr class="selected">';
                } else {
                    echo '<tr>';
                }

                echo "<th>$row</th>";
                for ($col = 1; $col <= $cols; $col++) {
                    $res = $col * $row;
                    echo "<td>$res</td>";
                }
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
    <form>
        <label for="rows">Lignes: </label>
        <input type="number" name="rows" id="rows" value="<?= $rows ?>">
        <br>
        <label for="cols">Colonnes: </label>
        <input type="number" name="cols" id="cols" value="<?= $cols ?>">
        <br>
        <?php
        if ($selected != -1) {
            echo "<input type='hidden' name='selected' value='$selected'>";
        }
        ?>
        <button type="submit">Redimensionner</button>
    </form>
</body>
</html>