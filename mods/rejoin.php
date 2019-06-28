<?
if (is_bot_oper($nih)) {
	sts("part $target");
	sts("join $target");
}
else stp($target,$nick.": Access denied");
?>