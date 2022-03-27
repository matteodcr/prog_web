<?php

// PHP a 7 ans de retard et n'aime pas les balises HTML5, on désactive donc les erreurs
libxml_use_internal_errors(true);

/**
 * Renvoie un lien twitter d'un thread présumé, ou `null` si aucun candidat n'est trouvé.
 */
function scrapeTwitterThreadUrl(string $url): ?string {
    $doc = new DOMDocument();

    if (!$doc->loadHTMLFile($url)) {
        die("Error parsing HTML");
    }

    foreach ($doc->getElementsByTagName('a') as $a) {
        $attr = $a->attributes;

        /** @var ?DOMAttr $target */
        $target = $attr->getNamedItem('target');
        /** @var ?DOMAttr $rel */
        $rel = $attr->getNamedItem('rel');
        /** @var ?DOMAttr $href */
        $href = $attr->getNamedItem('href');

        if (!is_null($target) && $target->value != '_blank') continue;
        if (!is_null($rel) && $rel->value != 'noopener') continue;
        if (!is_null($href) && preg_match('#^https://twitter.com/lamethodeFC/status/#', $href->value)) return $href->value;
    }

    return null;
}

// rfurl: « Radiofrance URL »
if (isset($_GET['rfurl'])) {
    $tweet_url = scrapeTwitterThreadUrl($_GET['rfurl']);
} else {
    $tweet_url = null;
}

if (is_null($tweet_url)) {
    // Pas de tweet, on renvoie une page vide
} else {
    echo json_decode(file_get_contents("https://publish.twitter.com/oembed?url=$tweet_url"))->html;
}
