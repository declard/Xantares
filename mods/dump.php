<?
$level=0;
$tabs='';
$save=fopen(localpath()."save.dat","w+");
if (!function_exists("save")) {
function save(&$var,&$key) {
	global $level,$save,$GLOBALS,$tabs;
	if (is_array($var)) {
		fputs($save,$tabs."a ".$key." {\n");
		$level++;
		$tabs.="\t";
		array_walk($var,"save");
		$tabs=substr($tabs,0,-1);
		$level--;
		fputs($save,$tabs."}\n");
	}
	elseif (is_string($var)) fputs($save,$tabs."s ".$key." ".strlen($var)." ".$var."\n");
	elseif (is_resource($var)) fputs($save,$tabs."r ".$key." ".$var."\n");
	elseif (is_bool($var) && $var) fputs($save,$tabs."b ".$key." true\n");
	elseif (is_bool($var) && !$var) fputs($save,$tabs."b ".$key." false\n");
	elseif (is_object($var)) {
		fputs($save,$tabs."o ".get_class($var)." ".$key." {\n");
		$level++;
		$tabs.="\t";
		array_walk($var,"save");
		$tabs=substr($tabs,0,-1);
		$level--;
		fputs($save,"}\n");
	}
	else fputs($save,$tabs."n ".$key." ".$var."\n");
}
}
unset($anek["file"]);
stp($nick,"Saving variables...",true);
foreach($GLOBALS as $key => &$var) {
	if ($key!="GLOBALS") {
		if (is_array($var)) {
			fputs($save,"a ".$key." {\n");
			$level++;
			$tabs.="\t";
			array_walk($var,"save");
			$tabs=substr($tabs,0,-1);
			$level--;
			fputs($save,$tabs."}\n");
		}
		elseif (is_string($var)) fputs($save,"s ".$key." ".strlen($var)." ".$var."\n");
		elseif (is_resource($var)) fputs($save,"r ".$key." ".$var."\n");
		elseif (is_bool($var) && $var) fputs($save,"b ".$key." true\n");
		elseif (is_bool($var) && !$var) fputs($save,"b ".$key." false\n");
		elseif (is_object($var)) {
			fputs($save,"o ".$key." {\n");
			$level++;
			$tabs.="\t";
			array_walk($var,"save");
			$tabs=substr($tabs,0,-1);
			$level--;
			fputs($save,"}\n");
		}
		else fputs($save,"n ".$key." ".$var."\n");
	}
}
fclose($save);
?>