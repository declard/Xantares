<?
if (!function_exists("maxvisitors")) {
foreach($dynamic_chans as $chan => $obj) $maxvisitors[$chan]=count($obj->nick);
unset($obj,$chan);
if (file_exists(localpath()."data/peak.dat")) {
	$file=file(localpath()."data/peak.dat");
	foreach($file as $line => $content) {
		$content=trim($content);
		$text=explode(" ",$content);
		if (is_numeric($text[1])) $maxvisitors[$text[0]]=$text[1];
	}
	unset($file);
}
function maxvisitors_raw($text) {
	maxvisitors(null,$text[4]);
}
function maxvisitors($nick,$chan) {
	global $maxvisitors,$dynamic_chans,$bot;
	if ($bot['nick']==$nick) return;
	$count=count($dynamic_chans[$chan]->nick);
	if ($maxvisitors[$chan]<$count || !array_key_exists($chan,$maxvisitors)) {
		$maxvisitors[$chan]=$count;
		stp($chan,'Ќовый рекорд посещаемости канала: '.$count.' человек');
	}
}
function save_maxvisitors() {
	global $maxvisitors;
	$file=fopen(localpath()."data/peak.dat",'w');
	foreach($maxvisitors as $chan => $count) if (is_numeric($count)) fputs($file,$chan.' '.$count."\n");
	fclose($file);
}
bind('join','maxvisitors');
bind(353,'maxvisitors_raw');
register_shutdown_function('save_maxvisitors');
}
if (isset($target_local)) if ($target_local[0]=='#' && array_key_exists($target_local,$maxvisitors)) stn($nick,'ћаксимальное количество посетителей на канале '.$target_local.' - '.$maxvisitors[$target_local].' человек');
?>