<?php

use Hekmatinasser\Verta\Verta;

function convertToGregorian($date)
{
    // Date format => 1400/1/5
    $date = explode('/', $date);

    return str_replace(',', '-', str_replace('/', '-', implode(',', Verta::getGregorian(
        $date[0],
        $date[1],
        $date[2]
    ))));
}
