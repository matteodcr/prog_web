[lire en ligne (certains éléments Markdown non-conventionnels marcheront mieux)](https://md.edgar.bzh/s/gBQ1GdU-0#)

DECORSAIRE Mattéo
ONGHENA Edgar

# PW -- CR TP 2

[TOC]

## Points d'accès WiFi

https://data.metropolegrenoble.fr/ckan/dataset/bornes-wifi-gratuites-de-grenoble

### 0 -- Visualisation

```
❯ file borneswifi_EPSG4326.csv
borneswifi_EPSG4326.csv: CSV text
```

`cat` nous affiche bien le contenu du fichier. Par contre, le fichier est encodé en Latin-1 (ISO 8859-1) et notre terminal est configuré en UTF-8. On  utilise donc l'utilitaire `iconv` pour faire la conversion. Le UTF-8 est bien plus courant et recommandé, on se simplifie la vie.

### 1 -- Comptage

Comptons le nombre de lignes:
```
❯ wc -l borneswifi.csv 
69 borneswifi.csv
```

En CSV, il y a habituellement une entrée par ligne. On a donc 69 points d'accès renseignés.

Il y a 69 - 1 = 68 antennes.

### 2 -- Points multiples

Nous voulons:
* Couper le texte à chaque virgule de sorte à garder le 2ème champ CSV: `cur -d, -f2 borneswifi.csv`
* Ne garder que les lignes uniques: `sort | uniq`
* Compter le nombre de lignes: `wc -l`

On construit la chaîne de commandes Linux suivante:
```
❯ cut -d, -f2 borneswifi.csv | sort | uniq | wc -l
59
```

Il y a 59 emplacements différents.

On veut maintenant connaître le nombre de points d'accès maximal au même endroit:
* On coupe le fichier de sorte à garder le 2ème champ CSV: `cur -d, -f2 borneswifi.csv`
* On compte le nombre de chaque occurence: `sort | uniq -c`
* On trie numériquement par ordre décroissant, en ignorant le padding initial: `sort -bnr`
* On ne garde que la première entrée: `head -1`
```
❯ cut -d, -f2 borneswifi.csv | sort | uniq -c | sort -bnr | head -1
      5 Bibliothèque Etudes
```

L'emplacement avec le plus d'antennes est 'Bibliothèque Etudes' avec 5 bornes WiFi.

## Traitements PHP

https://www.php.net/manual/en/function.array-multisort.php

## Antennes GSM

https://data.metropolegrenoble.fr/ckan/dataset/l-ensemble-des-antennes-gsm

### 1 -- Vue d'ensemble du fichier CSV

Il y a 101 lignes, dont un en-tête avec le nom des colonnes, soit 100 antennes. Le fichier comporte, en plus du fichier des points d'accès:
* Un identifiant unique ordinal, plus efficace à stocker et à indexer qu'un identifiant texte dans une base de données relationelle
* Un identifiant unique de l'opérateur exploitant (Free, Orange, SFR, Bouygues)
* Les générations de la technologie GSM supportées par l'antenne (2/3/4G)
* **Et ce qui a un intérêt considérable dans une démarche opendata:** un numéro unique [Cartoradio](https://www.cartoradio.fr), qui permet de faire le lien entre ce jeu de données et d'autres
  > Cartoradio est un site web créé en 2003 par l'Agence nationale des fréquences. Il recense toutes les antennes émettant à plus de 5 watts déclarées sur le territoire français, hormis celles de l'Aviation civile et des ministères de la Défense et de l'Intérieur2.
  > [name=Wikipedia]

### 2 -- Statistiques opérateurs

Nous voulons:
* Découper le fichier a tous les points-virgules et garder le 4ème champ (`OPERATEUR`): `cut -d';' -f4 <fichier>`
* Compter le nombre d'occurence de chaque ligne: `sort | uniq -c`

```bash
❯ cut -d';' -f4 DSPE_ANT_GSM_EPSG4326.csv | sort | uniq -c
     26 BYG
     18 FREE
      1 OPERATEUR
     26 ORA
     30 SFR
```

On voit que l'en-tête est inclus. On peut simplement l'ignorer, ou on aurait pu utiliser `tail -n +2 <fichier> | cut...` au début de la pipeline pour démarrer à la ligne 2.

### 3 -- KML validation

KML est une spécialisation du XML, ce dernier étant par design extensible. Afin de limiter les erreurs lors de l'écriture d'un document XML spécialisé par une application, on peut utiliser des validateurs qui prennent en paramètre un "schéma"; un document indiquant quelles balises et attributs sont valides et comment ils s'emboîtent. Un schéma XML peut être écrit au format <abbr title="XML Schema Definition">XSD</abbr>, un successeur au DTD.

En cherchant "kml filetype:xsd" sur Google, mon 5ème résultat de recherche est http://schemas.opengis.net/kml/2.3/ogckml23.xsd, qui semble être la version la plus récente. Ce site internet appartient à l'Open Geospatial Consortium, qui a normalisé le format KML après sa création par Google. Il est donc raisonnable de leur faire confiance quant à la concordance de ce schéma avec la spécification dont ils sont eux-mêmes les auteurs.

Sur un système Unix équipé de la commande suivante, on peut valider un fichier kml en exécutant:

```
xmllint --schema <schema>.xsd <fichier_kml>.kml --noout
```

Où le schema est celui accessible à l'URL ci-dessus. Notons que si le fichier est au format KMZ, il faut d'abord extraire le fichier KML de l'archive ZIP qu'est le fichier KMZ.

Sur Unix, on peut utiliser `unzip DSPE_ANT_GSM_EPSG4326.kmz` pour extraire le fichier `doc.kml` dans le dossier courant, ou encore `unzip -p DSPE_ANT_GSM_EPSG4326.kmz doc.kml` pour lire le fichier vers la sortie standard sans toucher au système de fichier, afin de l'envoyer vers une pipeline.

### 4 -- KML bis

Les fichiers XML sont en général très lourds syntaxiquement par rapport à d'autres formats comme du JSON ou du CSV. D'autant que dans ce cas précis -- probablement pour des raisons d'économie d'espace -- le fichier n'est même pas indenté. L'avantage est qu'ils sont donc très redondants, il est facile de détecter une erreur, par exemple une balise qui ne serait pas fermée. Ce format est de moins en moins utilisé au profit de formats plus légers, plus simples à parser ou plus simple à lire par des humains.

### 5 -- Top N opérateur

Cette partie a été implémentée par dessus la Q10 de la partie précédente, avec un formulaire à deux boutons, l'un recherchant des PA Wi-FI et l'autre des antennes GSM.

Version live ici: http://pw.edgar.bzh/Exercices/TP2/map/
