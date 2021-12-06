<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

//echo print_r($_POST);

//$data = json_decode($_POST['json']);

// Заполняем в коде D-M-Y
$dates = array("01-08-2014", "01-09-2014", "01-10-2014", "01-11-2014");
$sum = array(-100000, 34002.21, 34002.21, 34002.21);
//$dates = $data[0];
//$sum   = $data[1];
$m = count($dates); // число платежей

//Задаем базвый период bp
$bp=30; //30.41;
$cbp = round(365 / $bp);

//заполним массив с количеством дней с даты выдачи до даты к-го платежа
$days = [];
$days1 = [];
$days2 = [];
for ($k = 0; $k < $m; $k++) {
    $days[$k] = (strtotime($dates[$k]) - strtotime($dates[0])) / 86400;
}

//посчитаем Ек и Qк для каждого платежа
$e = [];
$q = [];
for ($k = 0; $k < $m; $k++) {
    $e[$k] = ($days[$k] % $bp) / $bp;
    $t_q = $days[$k] / $bp;

    if($t_q<1) {

     $q[$k] = round($t_q);
    } else {
     $q[$k] = floor($t_q);
    }
}

//Втупую методом перебора начиная с 0 ищем i до максимального приблежения с шагом s
function f($i){
    $x = 0;
    global $m, $sum, $e,$q;
    for ($k = 0; $k < $m; $k++) {
        $x = $x + $sum[$k] / ((1 + $e[$k] * $i) * pow(1 + $i, $q[$k]));
    }

    return $x;
}

function bisectionMethod($a,$b, $tol)
{
    $fa = f($a);
    $fb = f($b);

    while(True) {
        $c = 0.5 * ($a + $b);
        if (gmp_abs($a - $b) <= $tol) {
            return $c;
        }
        $fc = f($c);
        if (gmp_abs($fa) <= $tol || abs($fb) <= $tol) {
            return $a;
        }
        if (($fc > 0 && $fb < 0) || ($fc < 0 && $fb > 0)) {
            $a = $c;
            $fa = f($a);
        } else {
            $b = $c;
            $fb = f($b);
        }
    }


}


//$x = 1;
//$x_m = 0;
//$s = 0.0000001;
//
//while ($x > 0) {
//    $x_m = $x;
//    $x = 0;
//    for ($k = 0; $k < $m; $k++) {
//        $x = $x + $sum[$k] / ((1 + $e[$k] * $i) * pow(1 + $i, $q[$k]));
//    }
//
//    if (($x-$x_m) < 10){ // увеличим точность расчета
//	$s = 0.0000001; }
//    $i = $i + $s;
//}
//if ($x > $x_m) {
//    $i = $i - $s;
//}
//$i = $i + (($x -$x_m)*$s);

// считаем i с помощью метода деления пополам
$result = bisectionMethod(0, 1000000000000, 0.000000000000001);
//считаем ПСК
$psk = ($result * $cbp * 100 * 1000) / 1000;


echo $psk;


// Отключаем проверку на ошибки
ini_set('display_errors','Off');

?>

