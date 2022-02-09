<?php

function iter_month_days(int $year, int $month) {
    $first_day = strtotime("$year-$month-01");
    for ($current_day = $first_day;; $current_day += 24 * 3600) {
        $c_day_of_week = intval(date('w', $current_day));
        $c_day_of_month = intval(date('d', $current_day));
        $c_month = intval(date('m', $current_day));

        if ($c_month == $month) {
            yield [
                "day_of_month" => $c_day_of_month,
                "day_of_week" => ($c_day_of_week + 6) % 7,
            ];
        } else {
            return;
        }
    }
}
