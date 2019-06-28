<?
if (!function_exists("tell_anek_string")) {
function tell_anek_string($array,$target_local) {
	if (!count($array)) return;
	stp($target_local,trim(array_shift($array)));
	timer(2,"tell_anek_string",$array,$target_local);
}
}
if (!is_timer('tell_anek_string')) {
	$error=false;
	if (!count($anek['file'])) $anek['file']=GetDBIdxList($array['source']);
	if (!count($arg)) $idx=$anek['file'][rand(0,count($anek['file']))];
	elseif ($arg[0][0]=='№'&&substr($arg[0],1)<count($anek['file'])) $idx=$anek['file'][substr($arg[0],1)];
	else $error=true;
	if (!$error) {
		$send($target_local,"03 >>");
		$array=explode("\n",trim(GetDBByIdx($anek["source"],$idx)));
		unset($anek['file'][array_search($idx,$anek['file'])]);
		$anek['file']=array_values($anek['file']);
		unset($idx);
		timer(1,"tell_anek_string",$array,$target_local);
	}
	else $send($target_local,'Error');
}
else if (count($arg)&&($arg[0]=="стоп"||$arg[0]=="stop")) {
	del_timer("tell_anek_string");
	stp($target_local,$nick.": анекдот прерван.");
	unset($anek["current"]);
}
?>