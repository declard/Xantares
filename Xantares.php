<?
print("[".date("d.m.y",time())."][".date("H:i:s",time())."] Script started\n");
function localpath() {
	static $path=0;
	$path='/Bot/';
	return $path;
}

setlocale(LC_COLLATE,"ru_RU.CP1251");
setlocale(LC_CTYPE,"ru_RU.CP1251");
setlocale(LC_TIME,"ru_RU.CP1251");

define('COLOR',"\x03",false);
define('BOLD',"\x02",false);
set_time_limit(0);
require localpath().'DB.php';
$msgtimer=microtime(true);
$iftp=array();
$seen_target=array();
$dynamic_chans=array();
$timers=array();
$binds=array();
$dynamic_msgs=array();
### Анекдоты
$anek["source"]=localpath().'data/anekdots.dat';
if (!file_exists($anek["source"].'.db')) if (file_exists($anek["source"])) MakeDB($anek["source"],"- NEW -\r");
else print "Can't load anecdots file";
$anek["source"].='.db';
$anek["file"]=GetDBIdxList($anek["source"]);

### функция транслита
function translit($ss) {
	$w=array(
		'вв','w',
		"ё",'jo',
		"ж",'zh',
		"ч",'ch',
		"ш",'sh',
		"щ",'sch',
		"ю",'ju',
		"я",'ja');
	$ss=strtolower($ss);
	 for ($i=0; $i<count($w); $i+=2) $ss=str_replace($w[$i],$w[$i+1],$ss);
	$ss=strtr($ss,"абвгдезийклмнопрстуфхцъыьэ","abvgdezijklmnoprstufhc`y'e");
	return $ss;
}

function xreg($mask,$string) {
	$m=$s=0;
	while ($m<strlen($mask)) {
		if ($mask[$m]=='?') { if ($s==strlen($string)) return false; $m++; $s++; }
		elseif ($mask[$m]=='*') {
			$m++;
			if ($m==strlen($mask)) return true;
			while (!xreg(substr($mask,$m),substr($string,$s)) && $s<strlen($string)) $s++; if ($s==strlen($string)) return false; return true; }
		else {
			if ($mask[$m]==$string[$s] && $s<strlen($string)) { $m++; $s++; }
			else return false;
		}
	}
	if ($s==strlen($string)) return true;
	return false;
}

function remain_word_form($n,$forms) {
	$n='0'.$n;
	$n=substr($n,-2);
	if ($n[0]==1) return $forms[2];
	if ($n[1]==1) return $forms[0];
	if ($n[1]>=2&&$n[1]<=4) return $forms[1];
	return $forms[2];
}

function remain($format,$remain) {
	$flag=false;
	$res='';
	if ($remain<0) $func='ceil';
	else $func='floor';
	for($i=0;$i<strlen($format);$i++) {
		$c=$format[$i];
		if ($c=='%') {
			$flag=true;
			continue;
		}
		if (!$flag) $res.=$c;
		else {
			switch($c) {
				case '%':	$res.='%'; break;
				case 'd':	$n=$func($remain/24/3600);
					$res.=$n.' ';
					$res.=remain_word_form($n,array('день','дня','дней'));
					break;
				case 'h':	$n=$func($remain%(24*3600)/3600);
					$res.=$n.' ';
					$res.=remain_word_form($n,array('час','часа','часов'));
					break;
				case 'n':	$n=$func($remain%3600/60);
					$res.=$n.' ';
					$res.=remain_word_form($n,array('минута','минуты','минут'));
					break;
				case 's':	$n=$func($remain%60);
					$res.=$n.' ';
					$res.=remain_word_form($n,array('секунда','секунды','секунд'));
					break;
				default:	$res.='%'.$c;
			}
			$flag=false;
				
		}
	}
	return $res;
}


function bind($event,$func) {
	global $binds;
	if (!isset($binds[$event])) $binds[$event]=array();
	foreach($binds[$event] as $key => $action) if ($action==$func) return 0;
	array_push($binds[$event],$func);
	return 1;
}

function unbind($event,$func) {
	global $binds;
	if (!isset($binds[$event])) return 0;
	foreach($binds[$event] as $key => $action) if ($action==$func) {
		unset($binds[$event][$key]);
		return 1;
	}
	return 0;
}

class timer {
	var $time,$action,$count,$duration,$args=array();
}

function timer($time) {
	global $timers;
	$t = new timer;
	$params=array_slice(func_get_args(),1);
	if (is_numeric($params[0])) {
		$t->count=$params[0];
		array_splice($params,0,1);
		if (is_numeric($params[0])) {
			$t->duration=$params[0];
			array_splice($params,0,1);
		}
		else $t->duration=1;
	}
	else $t->count=1;
	$t->action=$params[0];
	array_splice($params,0,1);
	foreach($params as $key => $arg) array_push($t->args,$arg);
	$time+=microtime(true);
	$t->time=$time;
	$timers[]=$t;
}

function utimer($time) {
	global $timers;
	$t = new timer;
	$params=array_slice(func_get_args(),1);
	if (is_numeric($params[0])) {
		$t->count=$params[0];
		array_splice($params,0,1);
		if (is_numeric($params[0])) {
			$t->duration=$params[0]/1000;
			array_splice($params,0,1);
		}
		else $t->duration=1;
	}
	else $t->count=1;
	$t->action=$params[0];
	array_splice($params,0,1);
	foreach($params as $key => $arg) array_push($t->args,$arg);
	$time/=1000;
	$time+=microtime(true);
	$t->time=$time;
	$timers[]=$t;
}

function del_timer($name) {
	global $timers;
	foreach($timers as $key => $timer) if (substr($timer->action,0,strlen($name))==$name) array_splice($GLOBALS["timers"],$key,1);
}

function is_timer($name) {
	global $timers;
	foreach($timers as $key => $timer) if (substr($timer->action,0,strlen($name))==$name) return $key;
	return false;
}

function strfind() {
	$array=func_get_args();
	$string=$array[0];
	$array=array_slice($array,1);
	if (str_replace($array,"",$string)!=$string) return 1;
	return 0;
}

function strip($string,$mask='a') {
	$mask=str_replace('a','burc',$mask);
	if (strstr($mask,'c')) $string=preg_replace("/\x03\d{0,2}(,\d\d?)?/",'',$string);
	if (strstr($mask,'b')) $string=str_replace("\x02",'',$string);
	if (strstr($mask,'u')) $string=str_replace("\x1F",'',$string);
	if (strstr($mask,'r')) $string=str_replace("\x16",'',$string);
	$string=str_replace("\x0F",'',$string);
	return $string;
}

class message { var $text,$logtrgt=array(),$llog,$rlog; }

$bad_irc_characters=array();
$bad_irc_characters_replacement=array();
$bad_irc_characters_exclude=array(0x01,0x03,0x02,0x16,0x1F,0x0B,0x0A,0x0D);
for($i=0;$i<32;$i++) {
    if (array_search($i,$bad_irc_characters_exclude)!==false) continue;
    $bad_irc_characters[]=sprintf('%c',$i);
    $bad_irc_characters_replacement[]=sprintf('\\x%02x',$i);
}
foreach(array("\n"=>'\\n',"\t"=>'\\t',"\r"=>'\\r') as $c => $m) {
    $bad_irc_characters[]=$c;
    $bad_irc_characters_replacement[]=$m;
}

function st($text,$logtrgt,$llog,$rlog,$nick,$now=false) {
	global $dynamic_msgs,$bot,$strip_colors,
	    $bad_irc_characters,$bad_irc_characters_replacement;
	if (strtolower($nick)==strtolower($bot['nick'])) return;
	$st=new message;
	$text=str_replace($bad_irc_characters,$bad_irc_characters_replacement,rtrim($text,"\n\r"))."\n";
	if ($strip_colors) $st->text=strip($text,'a');
	else $st->text=$text;
	$st->logtrgt=$logtrgt;
	$st->llog=$llog;
	$st->rlog=$rlog;
	if (!$now) array_push($dynamic_msgs,$st);
	else array_unshift($dynamic_msgs,$st);
}

### Функция для упрощения отправки сообщений серверу в формате raw
function sts($text_local) {
	global $fp;
	fputs($fp,$text_local."\n");
}

### Функция для упрощения отправки сообщений в приват/ник в формате (цель,текст)
function stp($target_local,$text_local,$now=false) {
	global $dynamic_chans,$bot;
	$ta=array();
	if ($target_local[0]=="#") {
		$ta['targets']=$target_local;
		$ta['llog']="[";
		$ta['rlog']="] <".$bot["nick"]."> ".trim($text_local);
	}
	else {
		$ta['targets']="_PRIVAT";
		$ta['llog']="[";
		$ta['rlog']="] -> *".$target_local."* ".trim($text_local); }
	st("PRIVMSG ".$target_local." :".$text_local,$ta['targets'],$ta['llog'],$ta['rlog'],$target_local,$now);
}

### Функция для упрощения отправки нотисов в приват/ник в формате (цель,текст)
function stn($target_local,$text_local) {
	global $dynamic_chans;
	$ta=array();
	$ta['targets']=array();
	if ($target_local[0]=="#") {
		$ta['targets']=$target_local;
		$ta['llog']="[";
		$ta['rlog']="] -> -".$target_local."- ".trim($text_local);
	}
	else {
		$ta['targets']="_PRIVAT";
		$ta['llog']="[";
		$ta['rlog']="] -> -".$target_local."- ".trim($text_local);
	}
	st("NOTICE ".$target_local." :".$text_local,$ta['targets'],$ta['llog'],$ta['rlog'],$target_local);
}

###CTCP
function stc($target_local,$text_local) {
	global $dynamic_chans;
	$ta=array();
	$ta['targets']=array();
	if ($target_local[0]=="#") {
		$ta['targets']=$target_local;
		$ta['llog']="[";
		$ta['rlog']="] -> [".$target_local."] ".trim($text_local);
	}
	else {
		$ta['targets']="_PRIVAT";
		$ta['llog']="[";
		$ta['rlog']="] -> [".$target_local."] ".trim($text_local);
	}
	st("PRIVMSG ".$target_local." :".$text_local."",$ta['targets'],$ta['llog'],$ta['rlog'],$target_local);
}

###CTCP reply
function stca($target_local,$text_local) {
	global $dynamic_chans;
	$ta=array();
	$ta['targets']=array();
	if ($target_local[0]=="#") {
		$ta['targets']=$target_local;
		$ta['llog']="[";
		$ta['rlog']="] -> [".$target_local."] ".trim($text_local);
	}
	else {
		$ta['targets']="_PRIVAT";
		$ta['llog']="[";
		$ta['rlog']="] -> [".$target_local."] ".trim($text_local);
	}
	st("NOTICE ".$target_local." :".$text_local."",$ta['targets'],$ta['llog'],$ta['rlog'],$target_local);
}

function is_bot_oper($mask_local) {
	global $root;
	for($i=0;$i<count($root);$i++) if (xreg($root[$i],$mask_local)) return true;
	return false;
}

function putlog($target_local,$text_start,$text_end) {
	$current_time=date("H:i:s",Time());
	$current_date=date("Ymd",Time());
	if ($target_local[0]!="#") $target_local="_PRIVAT";
	$target_local=preg_replace("/[<>]/","\x20",$target_local);
	$fp_logs=fopen(localpath()."logs/".$current_date.$target_local.".log","a");
	fwrite($fp_logs,strip($text_start,"a").$current_time.strip($text_end,"a")."\r\n");
	fclose($fp_logs);
}

function bot_shutdown() {
	global $dynamic_chans;
	foreach($dynamic_chans as $chan  => $obj) putlog($chan,"[","] *** Connection closed.\r\n");
}
register_shutdown_function('bot_shutdown');

function ison($chan) {
	global $dynamic_chans;
	$chan=strtolower($chan);
	if (array_key_exists($chan,array_change_key_case($dynamic_chans))) return true;
	else return false;
}


class chan {
var $mode=array(),$topic,$nick=array();
function ison($n) { $n=strtolower($n); $other=array_change_key_case($this->nick); if (array_key_exists($n,$other)) return true; else return false; }
function snm($n,$m) { $other=array_change_key_case($this->nick); if (array_key_exists(strtolower($n),$other)) $this->nick[$n]=$m; }
function gcm($m) { if (array_key_exists($m,$this->mode)) return $this->mode[$m]; else return "-";}
function scm($m) {
	for($i=0;$i<strlen($m);$i++) {
		if ($m[$i]=="+") $give="+";
		elseif ($m[$i]=="-") $give="-";
		else $this->mode[$m[$i]]=$give;
	}
}
}

### Основные переменные
$file=localpath()."Xantares.conf";
if (!file_exists($file)) {
	print("Unable to load config file");
	exit(1);
}
else include($file);
unset($file);

$fp = fsockopen($server["ip"], $server["port"]);
if ($bot["pass"]) sts("PASS ".$bot["pass"]);
sts("NICK ".$bot["nick"]);
sts("USER ".$bot["ident"]." . . :".$bot["real_name"]);

$text=array();
$dynamic_server="";
while (!feof($fp) and !$dynamic_server) {
	$raw=fgets($fp);
	$text=explode(" ",trim($raw));
	if ($text[1]==001) $dynamic_server=trim(substr($text[0],1));
	if ($text[0]=="PING") sts("PONG :".trim(substr($text[1],1)));
}
if ($bot['ns_pass_file']) {
    $tp=fopen($bot['ns_pass_file'],"r");
    sts("ns id ".trim(fgets($tp)));
    fclose($tp);
}

foreach ($channels as $current_chan){
	putlog($current_chan,"Log started at: ",".");
	sts("join ".$current_chan);
}

$raw='';
while (!feof($fp)) {
	if (isset($chatfile)&&file_exists($chatfile)&&filesize($chatfile)) {
		$file=file($chatfile);
		foreach($file as $temp) {
			$array=explode(' ',$temp);
			stp($array[0],implode(' ',array_slice($array,1)));
		}
		fclose(fopen($chatfile,'w'));
	}
	$r=array($fp);
	if (stream_select($r,$w=NULL,$e=NULL,0,$script_rate)!=0) {
		$str=fgets($fp);
		$raw.=$str;
		if (substr($str,-1)=="\n") {
		    $raw=rtrim($raw,"\n\r");
		    include(localpath()."Xantares_external.php");
		    $raw='';
		}
	}
	if (count($timers))
		foreach($timers as $key => $timer)
			if ($timer->time<=microtime(true)) {
				if (!empty($timer->args)) call_user_func_array($timer->action,$timer->args);
				else call_user_func($timer->action);
				if ($timer->count==1) unset($timers[$key]);
				else {
					if ($timer->count>1) $timer->count--;
					$timer->time+=$timer->duration;
				}
			}
	if (count($dynamic_msgs) && microtime(true)>$msgtimer) {
		$st=new message;
		$st=$dynamic_msgs[0];
		array_splice($dynamic_msgs,0,1);
		$trgts=explode(",",$st->logtrgt);
		foreach($trgts as $trgt) if (array_key_exists($trgt,$dynamic_chans) || $trgt[0]!="#") putlog($trgt,$st->llog,$st->rlog);
		fputs($fp,$st->text);
		$msgtimer=microtime(true)+1;
		unset($st,$trgts,$trgt);
	}
}
fclose ($fp);
?>