<?
$file=fopen(localpath().'data/holidays.dat','r');
$p=array();
$date=date("d.m",time());
$flag=false;
while($temp=fgets($file)) {
	$p=explode("::",$temp);
	if ($p[0]==$date) {
		if ($p[1]=="B") $p[2]='���� �������� '.$p[2];
		stp($target,$nick.', �������:  �'.trim($p[2]).'�');
		$flag=true;
	}
}
if (!$flag) stp($target,$nick.', ������� ��� ���������');
fclose($file);
unset($flag,$p,$file);
?>
