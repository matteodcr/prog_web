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
    public string $mediaUrl;

    public DateTime $date;
    public string $parisDate;
    public int $week;
    public int $year;

    /**
     * @param $item mixed Une entrée fournie par rss-php
     */
    function __construct($item) {
        $this->title = $item->title;
        $this->description = $item->description;
        $this->mediaUrl = $item->enclosure->attributes()->url;

        $this->date = DateTime::createFromFormat('U', intval($item->timestamp), new DateTimeZone('UTC'));
        $parisDate = clone $this->date;
        $parisDate->setTimeZone(new DateTimeZone('Europe/Paris'));
        $this->parisDate = $parisDate->format('d/m/y G:i');
        $this->week = intval($parisDate->format('W'));
        $this->year = intval($parisDate->format('Y'));
    }
}
