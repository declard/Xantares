<?
if (!function_exists('fmul')) {
function fmul($x,$y) {
	$y=strrev($y);
	$o=0;
	$r=$t=array();
	for($i=0;$i<strlen($x)+strlen($y);$i++) $r[]=0;
	for($yi=0;$yi<strlen($y);$yi++) { // y iterator
		for($xi=0;$xi<strlen($x);$xi++) { // x iterator
			$c=$x[$xi]*$y[$yi]+$o;
			if ($c>9) {
				$o=floor($c/10);
				$c%=10;
			}
			else $o=0;
			$t[$xi]=$c;
		}
		if ($o) $t[strlen($x)]=$o;
		$o=0;
		for($i=0;$i<count($t);$i++) {
			$temp=$i+$yi;
			$r[$temp]+=$t[$i]+$o;
			if ($r[$temp]>9) {
				$o=1;
				$r[$temp]-=10;
			}
			else $o=0;
		}
		if (++$temp<count($r)) $r[$temp]=$o;
		$o=0;
		$t=array();
	}
	$r=implode('',$r);
	while(!substr($r,-1)&&strlen($r)>1) $r=substr($r,0,-1);
	return $r;
}
}
if (count($arg)) {
	if ($arg[0]<1) stp($target_local,'0');
	elseif ($arg[0]>400) stp($target_local,'ισυ');
	else {
		$t=microtime(true);
		$result='1';
		for($i=2;$i<=$arg[0];$i++) $result=fmul($result,strrev($i));
		stp($target_local,strrev($result));
		//stp($target_local,(microtime(true)-$t).' ρεκ.');
		unset($result,$x,$t);
	}
}
?>