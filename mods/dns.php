<?
if (!count($arg)) stp($target_local,'Usage: '.$cmd_char.'dns <hostname|ip>');
else {
	if (count(explode('.',$arg[0]))==4 && ip2long($arg[0]))
		if (($result=gethostbyaddr($arg[0]))!=$arg[0]) stp($target_local,'Host name: '.$result);
		else stp($target_local,"Can't resolve IP");
	else
		if (($result=gethostbyname($arg[0]))!=$arg[0]) stp($target_local,'IP address: '.$result);
		else stp($target_local,"Can't resolve host");
}
?>