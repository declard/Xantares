<?
$send='stp';
if (!isset($money)||time()-$money['time']>3600*24) { // Предположим, что информация устаревает чрз 24 часа
	$types=array('Доллар'=>'Доллар США','Евро'=>'Евро');
	$begin = '<td align="right">'; // фрагмент HTML-кода до полезных данных
	$end = '</td>'; // фрагмент HTML-кода после полезных данных
	$money['val'] = array();
	$array = file('http://cbr.ru/currency_base/daily.asp?C_month='.date('m',time()).'&C_year='.date('Y',time()).'&date_req='.date('d',time()).'%2F'.date('m',time()).'%2F'.date('Y',time()).'&d1='.date('d',time()));
	for($i=0;$i<count($array);$i++) foreach($types as $type => $id) if (trim($array[$i])=='<td>&nbsp;&nbsp;'.$id.'</td>') {
		$temp = explode($begin, $array[$i + 2]);
		$temp = explode($end, $temp[1]);
		$money['val'][$type]=$temp[0];
	}
	$money['time']=time();
}
foreach($money['val'] as $type => $val) $send($target_local,$type.' - '.$val);
?>