<?
$path_local=localpath()."data\frz.dat";
if (file_exists($path_local)) {
	$array_local=file($path_local);
	timer(rand(1,4),'stp',$target_local,$nick.', '.$array_local[rand(0,count($array_local)-1)]);
	unset($array_local);
}
else stp($target,"File not found");
?>