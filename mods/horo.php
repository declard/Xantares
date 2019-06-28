<?
$send='stp';
//$target_local=$icq_uin;
//$arg[0]=toUcfirst($arg[0]);
if (count($arg)) {
	$arg[0]=strtolower($arg[0]);
	$arg[0][0]=strtoupper($arg[0][0]);
}
if (!isset($horo_list)) $horo_list=array();
$signs=array('Овен','Телец','Близнецы','Рак','Лев','Дева','Весы','Скорпион','Стрелец','Козерог','Водолей','Рыбы');
if (in_array($arg[0],$signs)) {
	if (isset($arg[2])&&$arg[2]=="лав") $t='l';
	elseif (isset($arg[2])&&$arg[2]=="моб") $t='m';
	else $t='';
	if (!array_key_exists($t,$horo_list)||$horo_list['date']!=date('d',time())) {
		$horo_list['date']=date('d',time());
		$file = file('http://www.ignio.com/r/informer/1'.$t.'.html');
		for($i=4;$i<38;$i++) {
			$temp=strpos($file[$i],"'")+1;
			$horo_list[$t][$i-4]=str_replace('\n<p>',': ',substr($file[$i],$temp,strpos($file[$i],'</p>')-$temp));
		}
		unset($file);
	}
	if (isset($arg[1])&&$arg[1]=='вчера') $shift=0;
	elseif (isset($arg[1])&&$arg[1]=='завтра') $shift=2;
	else $shift=1;
	$send($target_local,$horo_list[$t][array_search($arg[0],$signs)+$shift*13]);
}
else $send($target_local,"Введите знак зодиака");
?>
