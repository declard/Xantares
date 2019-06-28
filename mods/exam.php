<?
$file=file(localpath().'data/exams.dat');
for($i=0;$i<count($file);$i++) {
	$t=explode(' ',$file[$i]);
	$send($nick,"\x0304".$t[0]."\x0303 - консультация по предмету \x0304".trim(implode(' ',array_slice($t,6)))."\x0303 в ".$t[2].'. Аудитория '.$t[4]);
	$send($nick,"\x0304".$t[1]."\x0303 - экзамен по предмету \x0304".trim(implode(' ',array_slice($t,6)))."\x0303 в ".$t[3].'. Аудитория '.$t[5]);
}
$send($nick,"\x0304\x02Memento mori!!111unouno");
unset($t,$file);
?>