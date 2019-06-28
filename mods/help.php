<?
$file=file(localpath()."data/cmds.dat");
foreach($file as $line) stp($nick,"\x0307".($line[0]!="\x03"?$cmd_char:'').$line);
?>        
