<?
if (count($arg)&&($arg[0]=='..'||$arg[0]=='.')) stp($target_local,'ому!');
else if (count($arg)) {
	$arg=explode(' ',strtolower(implode(' ',$arg)));
	print "\n";
	$dir=opendir('F:/FTP/');
	$path='F:/FTP/';
	$flag=true;
	while($flag&&$item=readdir($dir)) {
		$path_='';
		$temp=$arg;
		for($i=0;$i<count($arg);$i++)
			if (strtolower($item)==str_replace(' ','_',trim($path_.=array_shift($temp).' '))) {
				$flag=false;
				break;
			}
	}
	closedir($dir);
	if (!$flag) {
		$path_=trim($path_);
		$path.=str_replace(' ','_',$path_.'/');
		$dir=opendir($path);
		$flag=true;
		while($flag&&$item=readdir($dir)) if (explode('_',$item)==$temp) $flag=false;
		$path.=$item;
		closedir($dir);
		if (!$flag) {
			$file=file($path);
			$r=rand(0,count($file)-5);
			for($i=0;$i<4;$i++) stp($target_local,$file[$i+$r]);
		}
		else stp($target_local,"Can't find song");
	}
	else stp($target_local,"Can't find band");
}
?>