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
			if ($text[2]=="quiting:") $cause=" ���{��|��} �� �������: (".implode(" ",array_slice($text,3)).") ";
			elseif ($text[2]=="changing") $cause=" ������[�] ��� ";
			stp($target_local,$time."����� ".$nick.$cause);
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
			if ($text[2]=="quiting:") $cause=" ���{��|��} �� �������: (".implode(" ",array_slice($text,3)).") ";
			elseif ($text[2]=="changing") $cause=" ������[�] ��� ";
			if (substr($match,-1,1)==1) $sovp='�';
			elseif (substr($match,-1,1)>1 && substr($match,-1,1)<5) $sovp='�';
			else $sovp='�';
			if (isset($lastnum)) stp($target_local,"� ����� $match ���������".$sovp.", ��� $lastnum ����� ���������: ".$last.$cause.$time."�����.");
			else stp($target_local,"� ����� $match ���������".$sovp.": ".$last.$cause.$time."�����.");

		}
		elseif ($text[2]=="more") stp($target_local,"� ����� ����� 100 ������������ �������. ���������� ���������.");
		elseif ($text[2]=="no") stp($target_local,"� �� ����� �� ������ ������������ �������.");
		elseif ($text[1]=="haven't" and $text[2]=="seen") stp($target_local,"� �� ����� ".$text[3]);
		elseif ($text[4]=="right") stp($target_local,$text[0]." ������ � ����!");
		else stp($target_local,'Unrecognized answer: '.$text_line);
	}
}
function seen_time(&$text) {
	$other_local="";
	if (substr($text[0],-1)=="d") { $other_local=$other_local.str_replace("d","�",$text[0])." "; $text=array_splice($text,1); }
	if (substr($text[0],-1)=="h") { $other_local=$other_local.str_replace("h","�",$text[0])." "; $text=array_splice($text,1); }
	if (substr($text[0],-1)=="m") { $other_local=$other_local.str_replace("m","�",$text[0])." "; $text=array_splice($text,1); }
	$other_local=$other_local.str_replace("s","�",$text[0])." ";
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