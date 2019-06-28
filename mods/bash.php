<?
$line='';
$file=fopen('http://bash.org.ru/rss/','r');
while(!feof($file)) $line.=fread($file,1024);
$array=explode('</description>',$line);
array_splice($array,0,1);
$line=$array[rand(0,count($array)-1)];
$line=substr($line,strpos($line,'<description>')+22,-3);
$line=str_replace(array('&lt;','&gt;','&quot;','<br>'),array('<','>','"','<br />'),$line);
$array=explode('<br />',$line);
for($i=0;$i<count($array);$i++) $send($target_local,$array[$i]);
?>