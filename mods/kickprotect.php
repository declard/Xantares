<?
if (!function_exists('kickprotect')) {
function kickprotect($nick,$chan,$victim) {
	if ($victim=='') sts('kick '.$chan.' '.$nick);
}
bind('kick','kickprotect');
}
?>