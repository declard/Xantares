<?
bind("notice","seen");
if (!function_exists("seen")) {
function seen($nick,$target,$text_line) {
	$text=explode(" ",$text_line);
	global $seen_target;
	if (!count($seen_target)) return;
	$target_local=array_shift($seen_target);
	if ($nick=="SeenServ" && (ison($target_local) || $target_local[0]!="#")) {
		if ($text[1]=="last" and $text[2]=="saw") {
			$nick=$text[3]." ".$text[4];
			$text=array_slice($text,5);
			$time=seen_time($text);
			if ($text[2]=="quiting:") $cause=" выш{ел|ла} по причине: (".implode(" ",array_slice($text,3)).") ";
			elseif ($text[2]=="changing") $cause=" сменил[а] ник ";
			stp($target_local,$time."назад ".$nick.$cause);
		}
		elseif ($text[3]=="match(es),") {
			$match=$text[2];
			if (substr($text[4],0,6)!='sorted') {
				$lastnum=$text[7];
				$last=implode(" ",array_slice($text,11,$text[7]+3));
				$text=array_slice($text,17+$text[7]);
			}
			else {
				$last=implode(" ",array_slice($text,5,$text[2]+3));
				$text=array_slice($text,11+$text[2]);
			}
			$time=seen_time($text);
			if ($text[2]=="quiting:") $cause=" выш{ел|ла} по причине: (".implode(" ",array_slice($text,3)).") ";
			elseif ($text[2]=="changing") $cause=" сменил[а] ник ";
			if (substr($match,-1,1)==1) $sovp='е';
			elseif (substr($match,-1,1)>1 && substr($match,-1,1)<5) $sovp='я';
			else $sovp='й';
			if (isset($lastnum)) stp($target_local,"Я нашёл $match совпадени".$sovp.", вот $lastnum самых последних: ".$last.$cause.$time."назад.");
			else stp($target_local,"Я нашёл $match совпадени".$sovp.": ".$last.$cause.$time."назад.");

		}
		elseif ($text[2]=="more") stp($target_local,"Я нашел более 100 соответствий запросу. Необходимо уточнение.");
		elseif ($text[2]=="no") stp($target_local,"Я не нашел ни одного соответствия запросу.");
		elseif ($text[1]=="haven't" and $text[2]=="seen") stp($target_local,"Я не помню ".$text[3]);
		elseif ($text[4]=="right") stp($target_local,$text[0]." сейчас в сети!");
		else stp($target_local,'Unrecognized answer: '.$text_line);
	}
}
function seen_time(&$text) {
	$other_local="";
	if (substr($text[0],-1)=="d") { $other_local=$other_local.str_replace("d","д",$text[0])." "; $text=array_splice($text,1); }
	if (substr($text[0],-1)=="h") { $other_local=$other_local.str_replace("h","ч",$text[0])." "; $text=array_splice($text,1); }
	if (substr($text[0],-1)=="m") { $other_local=$other_local.str_replace("m","м",$text[0])." "; $text=array_splice($text,1); }
	$other_local=$other_local.str_replace("s","с",$text[0])." ";
	return $other_local;
}
}

if (!count($arg)) stp($target_local,"String is void");
elseif ($arg[0]=='') stp($target_local,"String is void");
else {
	array_push($seen_target,$target_local);
	stp("SeenServ","seen ".$arg[0]);
}
?>