<?
$file=file(localpath().'data/exams.dat');
for($i=0;$i<count($file);$i++) {
	$t=explode(' ',$file[$i]);
	$send($nick,"\x0304".$t[0]."\x0303 - ������������ �� �������� \x0304".trim(implode(' ',array_slice($t,6)))."\x0303 � ".$t[2].'. ��������� '.$t[4]);
	$send($nick,"\x0304".$t[1]."\x0303 - ������� �� �������� \x0304".trim(implode(' ',array_slice($t,6)))."\x0303 � ".$t[3].'. ��������� '.$t[5]);
}
$send($nick,"\x0304\x02Memento mori!!111unouno");
unset($t,$file);
?>