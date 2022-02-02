<?php
const UNICODE_URL = "https://util.unicode.org/UnicodeJsps/character.jsp?a=";

$word = $_GET['word'];
$word_len = mb_strlen($word);
$first_char = mb_substr($word, 0, 1);

function print_td(?string $char = null, ?int $cp = null, ?bool $highlight = false) {
    if (is_null($char) && !is_null($cp)) {
        $char = mb_chr($cp);
    } elseif (is_null($cp)) {
        $cp = mb_ord($char);
    } else {
        die('missing argument');
    }

    if ($highlight) {
        echo '<td class="highlight">';
    } else {
        echo '<td>';
    }

    $char_disp = $char;
    if ($char_disp == " ") $char_disp = "<i>SP</i>";

    echo "<b>$char_disp</b>";
    $codepoint = str_pad(dechex($cp), 4, "0", STR_PAD_LEFT);
    $url = UNICODE_URL . $codepoint;
    $name = mb_substr(exec("unicode -d $cp --brief"), 2);

    echo "<a href='$url' target='_blank' title='$name'>U+$codepoint</a>";

    echo '</td>';
}
?>

<table>
    <tbody>
        <tr>
            <?php
            for ($i = 0; $i < $word_len; $i++) {
                $char = mb_substr($word, $i, 1);
                print_td($char);
            }
            ?>
        </tr>
        <tr>
            <th colspan="16">Sur la même ligne Unicode que le premier caractère (<?= $first_char ?>):</th>
        </tr>
        <tr>
            <?php
            $first_code = mb_ord($first_char);
            $row_first_element = $first_code & 0b11111111111111111111111111110000;

            for ($i = 0; $i < 16; $i++) {
                $cp = $i + $row_first_element;
                print_td(null, $cp, $first_code == $cp);
            }
            ?>
        </tr>
    </tbody>
</table>
