<?  
if (is_bot_oper($nih) and $target[0]=="#") {
	stp($target,"��.. ������ � ����������.. ������� � $target ���� ����");
	sts("part $target");
} 
else stp($target,$nick.": Access denied");  
?>
