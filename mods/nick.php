<?  
if (is_bot_oper($nih)) {
	if (count($arg)==0) stp($target,"String argument expected");
	else {
		sts("Nick $arg[0]");
		stp($target,"Теперь меня зовут $arg[0]");
	}
} 
else stp($target,$nick.": Access denied");
?>
