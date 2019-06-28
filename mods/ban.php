<?  
if (is_bot_oper($nih)) {
	if (count($arg)>0) {
	    sts('KICK '.$target_local.' '.$arg[0].' :'.implode(' ',array_slice($arg,1)));
	    sts('mode '.$target_local.' +b '.$arg[0].'*!*@*');
	}
	else stp($target,"Требуется минимум 1 аргумент");
}
else stp($target,$nick.": Access denied");
?>
