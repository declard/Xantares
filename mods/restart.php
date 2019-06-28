<?
if (is_bot_oper($nih)) {
	sts("PRIVMSG :".$nick." :Restarting bot...\r");
	sts("QUIT\r");
	die(2);
}
else stp($target,$nick.": Access denied");
?>