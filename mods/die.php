<?  
if (is_bot_oper($nih)) {
	$other=implode(",",array_keys($dynamic_chans));
	sts("PRIVMSG $other :Script halted.\r");
	sts("QUIT :killed by $nick\n");
	die(1);
} 
else stp($target_local,"$nick: Access denied");
?>
