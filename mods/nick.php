<?  
if (is_bot_oper($nih)) {
	if (count($arg)==0) stp($target,"String argument expected");
	else {
		sts("Nick $arg[0]");
		stp($target,"������ ���� ����� $arg[0]");
	}
} 
else stp($target,$nick.": Access denied");
?>
