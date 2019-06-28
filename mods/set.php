<?
if (is_bot_oper($nih)) {
	$new_value=implode(" ",array_slice($arg,1));
	${$arg[0]}=trim($new_value);
	stp($target,"\$".$arg[0]." is now set to: ".$new_value);
	unset($new_value);
}
else stp($target_local,$nick.": Access denied");
?>