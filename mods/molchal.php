<?
if (!function_exists('molchal')) {
function molchal($text) {
	global $molchal_target;
	$text[4]+=19*3600;
	stp($molchal_target,$text[3]." молчал ".date("H:i:s",$text[4]).". Он вошел в сеть ".date("d M h:i:s",$text[5]).".");
	unbind(317,'molchal');
	unset($GLOBALS['molchal_target']);
}
}

if (!count($arg)) stp($target_local,'Void string');
else {
	bind(317,'molchal');
	sts('whois '.$arg[0].' '.$arg[0]);
	$molchal_target=$target_local;
}
?>