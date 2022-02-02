<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        table {
            border-collapse: collapse;
        }

        td {
            border: 1px solid black;
            padding: 0.1em;
        }

        td > * {
            display: block;
            width: 100%;
            text-align: center;
        }

        td > b {
            font-size: 4em;
        }

        td > b > i {
            font-size: 0.5em;
            color: darkgray;
        }

        .highlight {
            background: yellow;
        }
    </style>
</head>
<body>
    <form>
        <label for="word">Mot: </label>
        <input type="text" name="word" id="word" value="<?= $_GET['word'] ?: "" ?>">
    </form>
    <?php
    if (isset($_GET['word'])) {
        include 'unicode_output.php';
    }
    ?>
</body>
</html>