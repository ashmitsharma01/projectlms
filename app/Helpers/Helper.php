<?php 
function genRandomValue($length=5,$type='digit',$prefix=null){
    if($type=='digit'){
        $characters = date('Ymd').'123456789987654321564738291918273645953435764423'.time();
    } else {
        $characters = date('Ymd').'192837465TransactionRandomId987654321AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz111xxxBheemSwamixxx9OO14568O8xxxBikanerRajasthan34OO1'.time();
    }
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $prefix.$randomString;
}

function addSubDate($isDate, $val, $date, $format='d-m-Y', $adsSub='days'){
    //$isDate: +,- | $val: numericVal | $adsSub: days, months, year
    return date($format, strtotime($date. $isDate.$val.' '.$adsSub));
}

function timeAgo($date) {
   $timestamp = strtotime($date);   
   
   $strTime = ["second", "minute", "hour", "day", "month", "year"];
   $length = ["60","60","24","30","12","10"];

   $currentTime = time();
   if($currentTime >= $timestamp) {
        $diff     = time()- $timestamp;
        for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
            $diff = $diff / $length[$i];
        }

        $diff = round($diff);
        if($diff < 10){
            return dateConvert($date, 'Y-m-d h:i');
        }
        return $diff . " " . $strTime[$i] . "(s) ago ";
   }
}
function dateConvert($date=null,$format=null){
    if($date==null)
        return date($format);
    if($format==null)
        return date('d-m-Y',strtotime($date));
    else 
        return date($format,strtotime($date));
}
function timeConvert($time,$format=null){
    if($format==null)
        return date('H:i:s',strtotime($time));
    else 
        return date($format,strtotime($time));
}
function splitText($string=null, $splitBy = ','){
    if($string==null || $string==''){
        return [];
    }
    return explode($splitBy, $string);
}

function limit_text($text, $limit) {
  if (strlen($text) > $limit) {
        $text = substr($text, 0, $limit) . '...';
  }
  return $text;
}

function limit_words($string, $word_limit) {
    if (str_word_count($string, 0) > $word_limit) {
        $words = explode(" ",$string);
        return implode(" ",array_splice($words,0,$word_limit)).'...';
    }
    return $string;
}

function numberFormat($num){
    if (is_numeric($num)) {
        return sprintf("%.2f", (float) $num);
    } else {
        return 0;
    }
}

// Function to check if the value exists in a 2D array
function findInArray($array, $key, $value) {
    foreach ($array as $item) {
        if ($item[$key] === $value) {
            return $item; // Return the entire row if the value exists
        }
    }
    return null; // Return null if the value doesn't exist
}

function checkboxTickOrNot($value, $val_from=null){
    if($val_from=='view'){
        if($value == 1) return true; else return false;
    } else {
        if($value == 'on') return 1; else return 0;
    }
}

function numFormat($num){
    if (is_numeric($num)) {
        return sprintf("%.2f", (float) $num);
    } else {
        return 0;
    }
}

if (!function_exists('isActiveMenu')) {
    function isActiveMenu(array $routes, $class = 'show') {
        //return request()->is($pattern) ? $class : '';
        foreach ($routes as $route) {
            if (request()->routeIs($route)) {
                return true;
            }
        }
        return false;
    }
}
if (!function_exists('isActiveRoute')) {
    function isActiveRoute(array $routes, $class = 'active') {
        foreach ($routes as $route) {
            if (request()->routeIs($route)) {
                return $class;
            }
        }
        return '';
    }
}