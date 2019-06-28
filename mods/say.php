<?
if (is_bot_oper($nih)) {
	$other=implode(" ",array_slice($arg,1));
	stp($arg[0],$other);
}
else stp($nick,"Access denied!");
?>