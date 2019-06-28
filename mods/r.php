<?
do {
	if (!count($arg)) $d=date("w")%7+1;
	elseif (!is_numeric($arg[0])||(count($arg)>1&&!is_numeric($arg[1]))) {
		stp($target_local,"$nick: Value isn't a number");
		break;
	}
	elseif (substr($arg[0],0,2)=="0x") $d=intval(substr($arg[0],2),16);
	else $d=intval($arg[0]);
	
	if (count($arg)>1) $g=intval($arg[1]);
	else $g=5;

	if ($d===false) $d=date('w')%7+1;
	if ($d>7 or $d<1 or ($g!=5&&$g!=2)) {
		stp($target_local,"$nick: The number you have dialed is not available at the moment");
		break;
	}
	$path_local=localpath().'data/raspisanie'.$g.'.dat';
	if (!file_exists($path_local)) {
		stp($target,"File not found");
		break;
	}
	$array_local=file($path_local);
	foreach($array_local as &$item) $item=trim($item);
	if ($v3=($array_local[0]=='[v3]')) array_shift($array_local);
	$index=array();
	for($i=0;$array_local[$i][0]!='-' && $i<count($array_local);$i++) $index[]=$array_local[$i];
	if ($v3) {
		$tindex=array();
		for(++$i;$array_local[$i][0]!='-' && $i<count($array_local);$i++) $tindex[]=$array_local[$i];
	}
	$i=array_search("- $d -",$array_local)+1;
	$n=floor((time()/3600/24-3)/7+1)%2+1;
	$c=1;
	$d++;
	stp($nick,"\x0304".$array_local[$i++]);
	if (!$v3) {
		for(;$array_local[$i]!="- $d -" && $i<count($array_local);$i++) {
			if ($array_local[$i][0]=='#') continue;
			$temp=explode(' ',$array_local[$i]);
			$string="\x0304".$temp[1]." \x0303".$index[$temp[2]];
			if (array_key_exists(3,$temp)) $string.=' ('.$temp[3].', '.$temp[4].')';
			if (array_key_exists(5,$temp)) $string.=' (Подгруппа №'.$temp[5].')';
			if ($array_local[$i][0]==$n || !$array_local[$i][0]) timer($c++,"stp",$nick,$string);
			$i++;
		}
	}
	else {
		for(;$array_local[$i]!="- $d -" && $i<count($array_local);$i++) {
			if ($array_local[$i][0]=='#') continue;
			$temp=explode(' ',$array_local[$i]);
			$string="\x0304".$temp[1]." \x0303".$index[$temp[2]];
			if (array_key_exists(3,$temp)) $string.=' ('.$tindex[$temp[3]].', '.$temp[4].')';
			$type=$array_local[$i][0];
			if ($type=='n') $group=$n;
			elseif ($type=='i') $group=$n%2+1;
			elseif (array_key_exists(5,$temp)) $group=$temp[5];
			else $group='';
			if ($group) $string.=' (Подгруппа №'.$group.')';
			switch ($type) {
				case "$n": case '0': case 'n': case 'i': //timer($c++,"stp",$nick,$string);
				$send($nick,$string);
			}
		}
	}
} while (0);
unset($d,$c,$i,$n,$array_local,$temp,$index,$string,$type,$group);
?>

