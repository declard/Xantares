<?
if (is_bot_oper($nih)) {
	if (count($arg)>0) {
		if (ison($arg[0])) stp($target,$nick.": Channel is already exists");
		else {
			sts("join $arg[0]");
			stp($target,"���� �� $arg[0]");
		}
	}
	else stp($target,"��������� ���� ��������");
}
else stp($target,$nick.": Access denied");
?>
