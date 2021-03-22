<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    //$date='2021-03-10 00:00:00.000000';
 
    function gregorian_to_jalali($gy, $gm, $gd, $mod = '')
{
    $g_d_m = array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334);
    $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
    $days = 355666 + (365 * $gy) + ((int)(($gy2 + 3) / 4)) - ((int)(($gy2 + 99) / 100)) + ((int)(($gy2 + 399) / 400)) + $gd + $g_d_m[$gm - 1];
    $jy = -1595 + (33 * ((int)($days / 12053)));
    $days %= 12053;
    $jy += 4 * ((int)($days / 1461));
    $days %= 1461;
    if ($days > 365) {
        $jy += (int)(($days - 1) / 365);
        $days = ($days - 1) % 365;
    }
    if ($days < 186) {
        $jm = 1 + (int)($days / 31);
        $jd = 1 + ($days % 31);
    } else {
        $jm = 7 + (int)(($days - 186) / 30);
        $jd = 1 + (($days - 186) % 30);
    }
    return ($mod == '') ? array($jy, $jm, $jd) : $jy . $mod . $jm . $mod . $jd;
}

$date1='2021-03-10';
$edate1=explode('-',$date1);
$date2='2021-03-14';
$edate2=explode('-',$date2);

$convertedDate1=gregorian_to_jalali($edate1[0],$edate1[1],$edate1[2],'/');
$convertedDate2=gregorian_to_jalali($edate2[0],$edate2[1],$edate2[2],'/');


//echo $deff=date_diff($date1,$date2);

//echo 'Date1 is: '.$convertedDate1.' and Date2 is: '.$convertedDate2;

    //$secondDate= explode(' ',$date)[0];
   // $threthDate=explode('-',$secondDate);
  //  return gregorian_to_jalali($threthDate[0],$threthDate[1],$threthDate[2],'/');

//  $date1 = "2007-03-24";
//$date2 = "2009-06-26";

$diff = abs(strtotime($date2) - strtotime($date1));

$years = floor($diff / (365*60*60*24));
$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

printf( $days);

});
