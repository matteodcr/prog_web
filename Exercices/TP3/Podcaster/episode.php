<?php

/**
 * @param string $description Example: "durée : 00:05:48 - Le Journal des sciences - par : Natacha Triou - La..."
 * @return string "00:05:48"
 */
function parseDuration(string $description) {
    $matches = [];
    if (preg_match('/\d{2}:\d{2}:\d{2}/', $description, $matches) == 1) {
        return $matches[0];
    } else {
        return '<i>durée inconnue</i>';
    }
}

class Episode {
    public string $title;
    public string $description;
    public string $url;
    public string $mediaUrl;

    public DateTime $date;
    public string $parisDate;
    public int $weekday;
    public int $week;
    public int $year;

    /**
     * @param $item mixed Une entrée fournie par rss-php
     */
    function __construct($item, string $feedTitle = null) {
        $title = $item->title;

        $this->title = is_null($feedTitle) ? $title : "[$feedTitle] $title";
        $this->description = $item->description;
        $this->url = $item->link;
        $this->mediaUrl = $item->enclosure->attributes()->url;

        $this->date = DateTime::createFromFormat('U', intval($item->timestamp), new DateTimeZone('UTC'));
        $parisDate = clone $this->date;
        $parisDate->setTimeZone(new DateTimeZone('Europe/Paris'));
        $this->parisDate = $parisDate->format("d/m/y\u{00A0}G:i"); // Le caractère spécial est un nbsp
        $this->weekday = intval($parisDate->format('N')) - 1;
        $this->week = intval($parisDate->format('W'));
        $this->year = intval($parisDate->format('Y'));
    }
}
