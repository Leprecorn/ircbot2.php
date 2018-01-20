<?php

function get_string_between($string, $start, $end){
	$string = ' ' . $string;
	$ini = strpos($string, $start);
	if ($ini == 0) return '';
	$ini += strlen($start);
	$len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function delete_all_between($beginning, $end, $string) {
  $beginningPos = strpos($string, $beginning);
  $endPos = strpos($string, $end);
  if ($beginningPos === false || $endPos === false) {
    return $string;
  }

  $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

  return str_replace($textToDelete, '', $string);
}

function output($msg, $type = "-") {
	if (trim($msg) != "") {
		echo date("j.n.y H:i:s")." [".strtoupper($type)."] ".$msg.PHP_EOL;
	}
	if (strtoupper($type) == "ERROR") {
		die();
	}
}



?>