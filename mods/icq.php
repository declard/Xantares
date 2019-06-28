<?
if (!function_exists('icqtest')) {
	function icqtest() {
		global $icq;
		$stream=array($icq->socket);
		if (!stream_select($stream,$a=null,$b=null,0)) return;
		$icq->listen();
		if (!$icq->mess['text']) return;
		stp('XapoH','<'.$icq->mess["uin"].'> '.$icq->mess["text"]);
	}
	include $localpath.'ICQClass.php';
}
else unset($icq);
$icq = new ICQ("","");
if ($icq->connect()) print 'connected'.LF;
if ($icq->login()) print 'logged in'.LF;
utimer(100,0,'icqtest');
print $icq->lasterror.LF;
?>