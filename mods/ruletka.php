<?
if ($target_local[0]=='#') {
$array=array_keys($dynamic_chans[$target_local]->nick);
$temp=array();
foreach($array as $val) if ($val!=$bot['nick']&&$val!='ChanServ') $temp[]=$val;
$array=$temp;
$send($target_local,'4'.$nick.'03 ��� ����� �....');
if (!rand(0,1)) {
	$send($target_local,'3�������� ���� �����... ������� � �����, 04'.$nick);
	timer(2,1,'sts','kick '.$target_local.' '.$nick.' :���� ���.');
}
else {
	$temp=rand(0,count($array)-1);
	if ($array[$temp]==$nick) $temp=($temp+1)%count($array);
	$temp=$array[$temp];
	$send($target_local,'4'.$nick.'03 ��������.. ����� ��������� 04'.$temp.'03 ������ ���� ������� �������� �������');
}
}
?>