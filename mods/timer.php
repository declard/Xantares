<?
if (!function_exists("rt")) {
function rt() { stp("##povt","Tick..."); }
}
if (is_bot_oper($nih)) {
	if (!count($arg)) $arg[0]=2;
	if (count($arg)==1) $arg[1]=0;
	elseif (!is_numeric($arg[1])) $arg[1]=0;
	else $arg[1]=intval($arg[1]);
	del_timer("rt");
	if ($arg[0] !="stop") {
		if (!is_numeric($arg[0]) || $arg[0]<1) $arg[0]=2;
		timer($arg[0],$arg[1],"rt");
		$other=$arg[1]?" (".$arg[1]." repetetion[s])":".";
		stp("##povt","Timer has been started with ".$arg[0]." sec period".$other);
	}
}
else stp($target,$nick.": Access denied");
?>