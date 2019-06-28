<?  
if (is_bot_oper($nih) and $target[0]=="#") {
	stp($target,"Ёх.. только € присиделс€.. забрали с $target ¬сем пока");
	sts("part $target");
} 
else stp($target,$nick.": Access denied");  
?>
