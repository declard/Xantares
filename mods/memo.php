<?
$path=localpath().'data/memo.dat';
if (!count($arg)) {
	$array=file($path);
	$flag=false;
	foreach($array as $item) {
		$temp=explode(' ',$item);
		if (strtolower($nick)==strtolower(trim($temp[6]))) {
			stn($nick,"\x0304(".date('M d, Y, h:i:s',$temp[1]).") ������������ ".$temp[2].' ('.$temp[3].'@'.$temp[4].") \x0303������ �������� ���'.(($temp[0])?(" �� ������ ".$temp[5]):'').": \x0304".implode(array_slice($temp,7)));
			$flag=true;
		}
	}
	if (!$flag) stn($nick,"\x0304��� ��� ��� ���������");
	unset($array,$temp,$flag);
}
elseif ($arg[0]=='send') {
	$arg[0]=trim($arg[0]);
	$file=fopen($path,'a+');
	fputs($file,implode(' ',array_merge(array(($target[0]=='#'?1:0),time(),$nick,$ident,$host,$target),array_slice($arg,1))));
	fclose($file);
	stn($nick,"\x0303��, ����� �������!");
	unset($file);
}
elseif ($arg[0]=='erase') {
	$file=file($path);
	$array=array();
	$flag=false;
	foreach($file as $item) {
		$temp=explode(' ',$item);
		if (strtolower($nick)!=strtolower(trim($temp[6]))) $array[]=$item;
		else $flag=true;
	}
	if ($flag) {
		$file=fopen($path,'w');
		foreach($array as $item) fputs($file,trim($item)."\n");
		fclose($file);
		stn($nick,"\x0304�������� �������� �������");
	}
	else stn($nick,"\x0304��� ��� ��� ��������");
	unset($fie,$array,$file);
}
else stn($nick,'�����? ����? ����? ���� �����');
?>