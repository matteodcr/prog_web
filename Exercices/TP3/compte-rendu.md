% PW-DOM  Compte rendu de TP

# Compte-rendu de TP

Sujet choisi : 

## Participants 

* Un
* Deux
* ...

## Analyse d’un flux RSS de podcast

### Q1 — Tableau des podcasts

Dans un premier temps, nous n'avons pas cherché à créer une classe pour représenter les épisodes, on s'est contentés de manipuler les objets renvoyés par `rss-php`.
Côté CSS, on a utilisé une petite bibliothèque, [DoodleCSS](https://chr15m.github.io/DoodleCSS/), pour donner un peu de caractère à la page sans trop s'embêter.

### Q2 — Intercalaire hebdomadaire

Dans un premier temps, on a refactorisé une grosse partie du code dans une classe `Episode` que l'on construit avec un "item" du flux RSS. Le constructeur de la classe parse des données, formatte des dates sur le fuseau horaire `Europe/Paris` (au lieu d'UTC), et pré-calcule des informations utiles pour la suite.

Pour implémenter les intercalaires, on a rajouté une variable `$last` dans l'itération sur les épisodes, qui vaut le dernier épisode. En comparant `$episode->week` et `$last->week`, on peut savoir s'il y a eu un changement de semaine, et donc la nécessité d'ajouter un intercalaire.

**Problème rencontré :** On s'est aussi rendu compte plus tard que nous n'avions pas assez testé après cette re-factorisation ; une propriété `link` de l'ancien modèle était encore utilisée dans la boucle de rendu HTML (et indéfinie). Elle a été fixée à la question d'après et renommée `url`, pour rester cohérent avec la propriété `mediaUrl`.

### Q3 — Tableau hebdomadaire

La plus grosse difficulté a été de trouver comment passer d'un tableau d'épisodes en une dimension à un tableau où les épisodes sont groupés par semaine. La première tentative a été de créer une fonction générique `array_partition(array $arr, callable $get_key)` similaire au `uniq` d'UNIX qui grouperait les éléments d'un tableau pour lesquels une fonction `$get_key` reverrait la même valeur.

Mais on s'est rendus compte qu'il y avait souvent plusieurs épisodes le même jour, et c'était tout de suite moins pratique de fonctionner comme ça. Ensuite, on a essayé avec un tableau 2D initialisé avec beaucoup plus de semaines qu'il n'en faut (ou chaque semaine est elle-même un tableau de 5 tableaux d'épisodes), mais ça alloue beaucoup de mémoire pour rien et nécessite un filtrage des semaines vides à la fin.

Enfin, on s'est posé sur une version bien impérative similaire à la méthode ci-dessus, mais où les semaines sont insérées au fur et à mesure qu'on en a besoin, avec une clé basée sur le numéro de semaine et l'année, pour permettre un tri à la toute fin. Cela a vite fonctionné et on s'en est contenté.

**Problème rencontré :** Un oubli d'échapper les titres des épisodes de podcast avant de les insérer dans l'HTML a cassé le document HTML (y compris dans des versions précédentes). Cela a été fixé grâce à `htmlspecialchars`, qui échappe plein de caractères avec des codes du type `&gt;`.

### Q4 — Plusieurs podcasts

En préparation de la Q4 qui demande de modifier la Q2 et la Q3, les deux dernières questions ont été fusionnées pour fonctionner côte à côte. Un query-parameter `?view` a été ajouté et prend soit aucune valeur, soit `weeks`, soit `table`:

```php
$view_as_table = false;
if (isset($_GET["view"])) {
    switch ($_GET["view"]) {
        case "weeks":
            break;
        case "table":
            $view_as_table = true;
            break;
        default:
            die("Unknown view type: " . $_GET["view"]);
    }
}
```

Selon la vue demandée, le corp du `<body>` inclus ou bien `view_table.php`, ou bien `view_calendar.php`. Ainsi, les deux versions cohabitent dans les mêmes commits.

Le reste de la question 4 a été relativement simple:
  * Introduction d'une constante `FEED_URLS` qui prend une liste de flux RSS
    ```php
    const FEED_URLS = [
        'https://radiofrance-podcast.net/podcast09/rss_14312.xml',
        'https://radiofrance-podcast.net/podcast09/rss_10076.xml',
    ];
    ```
  * Double itération sur les flux & les items de ces derniers (au lieu de juste les items d'un flux) pour construire le tableau `$episodes`
  * Pour faire la différence entre les podcasts sur le rendu HTML, la classe `Episode` prend un nouveau paramètre optionnel `$feedTitle`.
    S'il est indéfini, le titre de l'épisode est celui indiqué dans le RSS.
    Sinon, on ajoute en préfixe "[Nom du podcast] ".

### Q5 — Attributs du MP3

`mp3info` ne reconnait pas les ID3V2 (uniquement V1) donc il ne marchait pas du tout. `id3v2` fonctionnait, mais ne nous donnait pas les informations recherchées.
En utilisant `mediainfo` ([apt ubuntu](https://packages.ubuntu.com/impish/mediainfo)), on a les résultats suivants:

```
❯ mediainfo podcast.mp3 | grep -E '(Bit rate +:)|(Format settings +:)|(Channel)'
Format settings                          : Joint stereo / MS Stereo
Bit rate                                 : 128 kb/s
Channel(s)                               : 2 channels
```

Le fichier est donc en stéréo (confirmé par la présence de 2 canaux) et son bitrate est 128 kb/s.

### Q6 — Ré-encodage

Pour convertir le podcast en mono 32kbps, la commande suivante a été utilisée : `lame -m m -b 32 podcast.mp3 podcast_compressed.mp3`. `mediainfo` nous confirme que les paramètres sont ceux attendus sur le fichier de sortie. Enfin, par curiosité, on regarde quel gain a été fait:

```
❯ du -h podcast*.mp3
5,5M	podcast.mp3
1,4M	podcast_compressed.mp3
```

Cela n'a rien d'étonnant. On divise le bitrate par 4 et le nombre de canaux par 2, soit un gain théorique de facteur 8, sauf qu'avec la compression du MP3 qui est généralement plus efficace sur des gros fichiers, le gain effectif est ici environ de facteur 4.

### Q7 — Liens twitter (des embeds ici)

Certains podcasts, par exemple [_Chimie ultra-froide, un sujet ultra-chaud_](https://www.franceculture.fr/emissions/la-methode-scientifique/chimie-ultra-froide-un-sujet-ultra-chaud), ont un fil Twitter associé renseigné sur la page du podcast. Ici, on lit :

> Retrouvez le [thread](https://twitter.com/lamethodeFC/status/1504112565177229314?s=20&t=ilYRkNgJgm71CifgqQlNGQ) de l’émission du jour sur le fil twitter de La Méthode Scientifique.

On peut imaginer effectuer une requête nous mêmes pour récupérer cette information. Par soucis de stabilité, comme la phrase ci-dessus pourrait changer de formulation, il semble raisonnable de simplement chercher un élément `<a target="_blank" rel="noopener" href="https://twitter.com/lamethodeFC/status/*">` (où `*` représente n'importe quoi).

Comme charger des centaines de tweets d'un seul coup prend un temps très élevé, je me suis permis d'utiliser une bibliothèque HTML/JS, [htmx](https://htmx.org), qui ici permet de ne charger les tweets que lorsque la case du tableau intersecte le viewport. Voici le fonctionnement détaillé :
  * Une page `twitter_thread.php?rfurl` prend en paramètre un URL de podcast Radiofrance. Il scrappe la page pour chercher un URL de tweet envoyé par @lamethodeFC et renvoie soit une page vide si rien n'est trouvé, soit une page qui contient juste un "embed" (une `<iframe>` du tweet).
  * Grâce à la bibliothèque HTMX, il est possible très facilement, sans JavaScript, de charger des morceaux d'HTML à l'intérieur d'un élément lorsque celui-ci reçoit un évènement configuré. C'est très puissant et configurable.
    * Dans notre cas, on utilise le code suivant :
      ```html
      <div
        class="tweet-container"
        hx-get="twitter_thread.php?rfurl=<?= htmlspecialchars(urlencode($episode->url)) ?>"
        hx-trigger="intersect once queue:first"
      >
        <small><i>chargement du tweet</i></small>
      </div>
      ```
      `hx-get` spécifie l'URL où se trouve le fragment d'HTML à charger
      
      `hx-trigger` indique qu'il faut charger le fragement lorsque l'élément `<div>` intersecte le viewport, uniquement la première fois que ça arrive, et qu'il faut charger en premier les évènements reçus en dernier (ça permet de charger en priorité ce que l'utilisateur a devant ses yeux)
  * Le tweet se retrouve donc inclus dans les cellules du tableau lorsque celles-ci deviennent visibles, ce qui minimise le nombre de requêtes inutiles et permet à la page de charger vite, quitte à être incomplète au début

Pour expérimenter avec cette fonctionnalité, il faut définit `?twitter` en query-parameter. Un lien en haut de la page permet de le faire facilement. Elle n'est implémentée que sur la vue hebdomadaire.

### Q8 — Liens twitter dans le mp3

D'après la spécification ID3, il y a plusieurs méthodes qui permettraient de stocker un URL de tweet dans les métadonnées d'un fichier MP3 :
  * [Créer son propre identifiant](https://id3.org/id3v2.3.0#ID3v2_frame_overview) de "frame" (sur 4 octets). Le gros risque est d'avoir une collision de nom avec quelqu'un d'autre qui utiliserait le même nom. De plus, la majorité des logiciels n'arriveraient pas bien à décoder cette frame.
  * Utiliser une ["user-defined text frame"](https://id3.org/id3v2.3.0#User_defined_text_information_frame), définie spécifiquement pour des extensions du format. Celle-ci prend additionnellement une description et une valeur. Une description suffisamment longue ou contenant un URL nous appartenant garantit l'absence de collision.
  * Mieux qu'au-dessus, utiliser une ["user-defined URL link frame"](https://id3.org/id3v2.3.0#User_defined_URL_link_frame). Le fonctionnement est le même, mais la valeur doit être un URL.
  * Utiliser une ["private frame"](https://id3.org/id3v2.3.0#Private_frame) au fonctionnement similaire aux 2 ci-dessus, mais qui encourage l'usage d'une adresse e-mail comme nom de propriété.
  * C'est pas forcément très adapté, mais il existe aussi un frame `#WPUB Publishers official webpage` qui peut correspondre.

Dans notre cas, cela requièrerait de modifier les tags ID3 des fichiers MP3, par exemple dans le lien de téléchargement qu'on fournit. Il n'y a probablement pas besoin de ré-encoder le fichier, mais il faudrait quand même les télécharger et les modifier, ce qui utilise de la bande-passante. Il est aussi probable que cela commence à poser des problèmes légaux comme il s'agit de redistribution avec modifications.