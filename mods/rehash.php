<?
if (is_bot_oper($nih)) {
	$file=localpath()."Xantares.conf";
	if (!file_exists($file)) stp($nick,"Error loading conig file");
	else include($file);
	if (isset($file)) unset($file);
	stp($nick,"Rehashing file...");
}
else stp($target,$nick.": Access denied");
?>