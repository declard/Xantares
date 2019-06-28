<?
if (!function_exists('sum')) {
function sum($x,$y) {
	$x=strrev($x);
	$y=strrev($y);
	$max=MAX(strlen($x),strlen($y));
	$min=MIN(strlen($x),strlen($y));
	$o=0;
	$r='';
	for($i=0;$i<$max;$i++) {
		$c=$o;
		if ($i<strlen($x)) $c+=$x[$i];
		if ($i<strlen($y)) $c+=$y[$i];
		if ($c>9){
			$o=1;
			$c-=10;
		}
		else $o=0;
		$r.=$c;
	}
	$r.=$o;
	$r=strrev($r);
	while(!$r[0]&&strlen($r)>1) $r=substr($r,1);
	return $r;
}

function mul($x,$y) {
	$x=strrev($x);
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
	$r=strrev(implode('',$r));
	while(!$r[0]&&strlen($r)>1) $r=substr($r,1);
	return $r;
}

function div2($x) {
	$o=0;
	$r='';
	for($i=0;$i<strlen($x);$i++) {
		$c=$o*10+$x[$i];
		$o=$x[$i]%2;
		$r.=($c-$o)/2;
	}
	while(!$r[0]&&strlen($r)>1) $r=substr($r,1);
	return $r;
}
}
if (count($arg)) {
	$temp=sum('1',$arg[0]);
	if ($temp[strlen($temp)-1]%2) $temp=mul($temp,div2($arg[0]));
	else $temp=mul(div2($temp),$arg[0]);
	stp($target_local,$temp);
}
?>