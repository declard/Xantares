<?
$kog=null;
$gorod=strtolower(implode(" ",array_slice($arg,0)));
$gorod_file=file(localpath().'data/city.dat');
$p=array();
for ($i=0; $i<count($gorod_file); $i++){
	$p=explode("::",$gorod_file[$i]);
	if (strtolower($p[1])==$gorod) {
		$kog=$p[0];
		break;
	}
}
if ($kog) {
	$temp=array();
	$file = fopen("http://climate.sergiusd.ru/mob0.6.3/".$kog."/3.txt", "r");
	$read = fgetss($file, 1024);
	fclose($file);
	for($i=11;$i<strlen($read);) {
		if ($i+32>strlen($read)||substr($read,$i+32,1)!='.') $len=26;
		else $len=27;
		$temp[]=substr($read,$i,$len);
		$i+=$len;
	}
	if (!function_exists("wind")) {
	function wind($smv) {
		$smv--;
		static $wind=array("�����","���.","�-����.","����.","���-����.","�����","���-���.","���.","�-���.",'����������');
		if (array_key_exists($smv,$wind)) return $wind[$smv];
		return 'Unknown';
	}
	function sky($smv) {
		$smv--;
		static $sky=array("����","��������� ����������","�������","���������� ����������, �����","�����","���� � �����","����","�����","����������",'a' => "�����������",'b' => "���������� ����������","c" => "���������� ����������, �����");
		if (array_key_exists($smv,$sky)) return $sky[$smv];
		return 'Unknown';
	}
	}
	$timeofday=array('�����','�����','���','�������');
	stp($nick,'03� ������ '.$gorod.' �� '.substr($read,0,10).' ���������:');
	$stod=$read[10];
	for($i=0,$n=0;$i<10;$i++) {
		$read=$temp[$i];
		$tod=$timeofday[($stod+$i)%4];
		if (!(($stod+$i)%4) && $i)
			if (!$n++) stp($nick,'03������ �� ��������� ����:');
			else break;
		$degree=str_replace('..',' ',trim(substr($read,1,8)));
		if (strstr($degree,'  ')) {
			$degree=trim(substr($degree,0,-1));
			$shift=-1;
		}
		else $shift=0;
		stp($nick,'04'.$tod.': 03'.sky($read[0]).', '.$degree.' ��������, �������� '.str_replace('..','-',trim(substr($read,9+$shift,8))).' ��.��.��., ����� '.wind($read[17]).' '.$read[18].'�/�, ��������� '.str_replace('..','-',trim(substr($read,20))).'%');
	}
}
else stp($nick,"04� �� ���� ������ ������!");
?>
