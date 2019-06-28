<?
### Обработка пинга
if (substr($raw,0,4)=="PING") sts("PONG :".trim(substr($raw,6)));
### Обработка именнованых событий
elseif ($raw[0]==":") {
	$text=explode(" ",$raw);
	$nih=substr($text[0],1);
	$nih=trim($nih);
	if ($nih!=$dynamic_server and $nih!=$bot["nick"]) {
		$temp=explode("!",$nih,2);
		$nick=trim($temp[0]);
		$temp=explode("@",$temp[1]);
		$ident=trim($temp[0]);
		$host=trim($temp[1]);
		unset($temp);
	}
	else $nick=$nih;
	$command=trim($text[1]);
	#$command=str_replace(":","",$command);
	### В переменных лежат: $nick - ник инициализатора, $ident - его идент, $host - его хост,
	### $command - команда
	switch ($command) {
		case 353:
			### Сохранение списка ников с их статусами на канале
			$chan_local=$text[4];
			$other=implode(" ",array_values(array_slice($text,5)));
			$other=trim($other);
			$nicks_local=explode(" ",$other);
			$nicks_local[0]=str_replace(":","",$nicks_local[0]);
			foreach($nicks_local as $nick_local) {
				$nick_local=trim($nick_local);
				if ($nick_local[0]=="+") { $nick_local=substr($nick_local,1); $status_local="+"; }
				elseif ($nick_local[0]=="%") { $nick_local=substr($nick_local,1); $status_local="%"; }
				elseif ($nick_local[0]=="@") { $nick_local=substr($nick_local,1); $status_local="@"; }
				else $status_local="n";
				$dynamic_chans[$chan_local]->nick[$nick_local]=$status_local;
			}
			unset($chan_local);
		break;
		case 332:
			$chan_local=$text[3];
			$other=implode(" ",array_values(array_slice($text,4)));
			$other=str_replace(":","",trim($other));
			$dynamic_chans[$chan_local]->topic=$other;
		break;
		case 324:
			$chan_local=$text[3];
			$other=implode(" ",array_values(array_slice($text,4)));
			$other=str_replace(":","",trim($other));
			$dynamic_chans[$chan_local]->scm($other);
		break;
		case "PRIVMSG":
			$target=trim($text[2]);
			$target=str_replace(":","",$target);
			$text=array_values(array_slice($text,3));
			$text[0]=trim(str_replace(":","",$text[0]));
			$other=array_pop($text);
			$other=trim($other);
			array_push($text,$other);
			$text_line=implode(" ",$text);
			
			if ($target==$bot["nick"]) $target_local="_PRIVAT";
			else $target_local=$target;
			if ($text_line[0]=="\x01") {
				$other=$text;
				$other[0]=str_replace("\x01","",$other[0]);
				array_push($other,str_replace("\x01","",array_pop($other)));
				if ($other[0]=="ACTION") putlog($target_local,implode(" ",array_slice($other,1)),'IACTION');
				else putlog($target_local,$other[0],implode(" ",array_slice($other,1)),'ICTCP');
			}
			else if (!is_me($target)) putlog($target_local,$text_line,'ICHANMSG');
			else putlog($target_local,$text_line,'IPRIVMSG');
			
			### Реакция на ник бота (отключено)
			#if (strstr(strtolower(strip($text_line,"a")),strtolower($bot["nick"])) and is_numeric(array_search(strtolower($target),$talk_chans)) and strtolower($nick)!="Хентай") include(localpath()."mods/fraza.php");
			### Загрузка модулей
			if (substr($text[0],0,1)==$cmd_char and $text[0][1]!="") {
				$inc_module=strip(translit(substr($text[0],1)),"a");
				$exceptions=array("/",".","\\","con","aux","|",">","<");
				if (strfind($inc_module,"/",".","\\","con","aux","|",">","<")) stp($target,$nick.": access denied :E");
				else {
					$inc_module=localpath()."mods/".trim($inc_module).".php";
					if (file_exists($inc_module)) {
						$arg=array_values(array_slice($text,1));
						if ($target==$bot["nick"]) $target_local=$nick;
						else $target_local=$target;
						include($inc_module);
						unset($target_local,$arg);
					}
					else if ($show_module_load_failure) stp($target,"Can't find module!");
				}
			}
			$other=implode(" ",$text);
			$other=trim($other);
			$text=explode(" ",$other);
			if (ord($text[0][0])==1) {
				switch (strtolower(str_replace("\x01",'',$text[0]))) {
				case "version":
							stca($nick,"VERSION PHP bot v".$bot['version'].". Created by Tamahome and XapoH");
							stca($nick,"VERSION Дополнительная благодарность Kx113 за подсказанную функцию отслеживания изменений потоков");
							break;
				case "ping":
							stca($nick,"PING ".str_replace("\x01","",$text[1]));
							break;
				case "userinfo":
				case "finger":
							stca($nick,"FINGER PHP Bot");
							break;
				case "time":
							stca($nick,"TIME ".date("H:i:s",Time()));
							break;
				}
			}
		break;
		case "NOTICE":
			$target=trim($text[2]);
			$target=str_replace(":","",$target);
			$text=array_values(array_slice($text,3));
			$text[0]=trim(str_replace(":","",$text[0]));
			$other=array_pop($text);
			$other=trim($other);
			array_push($text,$other);
			$text_line=implode(" ",$text);
			if (is_me($target)) putlog($target,$text_line,'IPRIVNOTC');
			else putlog($target,$text_line,'ICHANNOTC');
		break;
		case "JOIN":
			$target=trim($text[2]);
			$target=str_replace(":","",$target);
			if (!is_me($nick)) {
				putlog($target,$target,'IJOIN');
				$dynamic_chans[$target]->nick[$nick]="n";
				if ($greet_on_join && $nick!="ChanServ" && $nick!="SeenServ" && $nick!="hentai") timer(rand(1,4),'stp',$target,"Привет, $nick");
			}
			else {
				if (!array_key_exists(strtolower($target),array_change_key_case($dynamic_chans))) $dynamic_chans[$target]=new chan;
				sts("mode ".$target);
				putlog($target,$target,'IBOTJOIN');
			}
		break;
		case "PART":
			$target=trim($text[2]);
			$target=str_replace(":","",$target);
			$text=array_values(array_slice($text,3));
			if (count($text)!=0) {
				$text[0]=str_replace(":","",$text[0]);
				array_push($text,trim(array_pop($text)));
				$comment=" (".implode(" ",$text).")";
			}
			else $comment=".";
			if (!is_me($nick)) {
				putlog($target,$target,$comment,'IPART');
				unset($dynamic_chans[$target]->nick[$nick]);
			}
			else {
				putlog($target,$target,'IBOTPART');
				unset($dynamic_chans[$target]);
			}
			unset($comment);
		break;
		case "KICK":
			$target=trim($text[2]);
			$target_nick=trim($text[3]);
			$text=array_values(array_slice($text,4));
			if (count($text)!=0) {
				$text[0]=str_replace(":","",$text[0]);
				array_push($text,trim(array_pop($text)));
				$comment=" (".implode(" ",$text).")";
			}
			else $comment=".";
			if (!is_me($target_nick)) {
				putlog($target,$target_nick,$comment,'IKICK');
				unset($dynamic_chans[$target]->nick[$target_nick]);
			}
			else {
				putlog($target,$comment,'IBOTKICK');
				unset($dynamic_chans[$target]);
				if ($join_after_kick) {
					putlog($target," *** Attempt to rejoin...",'RAW');
					sts("join $target");
				}
			}
			unset($comment,$target_nick);
		break;
		case "MODE":
			if ($text[2][0]=="#") {
				$chan=trim($text[2]);
				$text=array_values(array_slice($text,3));
				$text[0]=str_replace(":","",$text[0]);
				$mode=$text[0];
				if (count($text)>=2) $nicks=array_values(array_slice($text,1));
				for($i=0,$nv=0;$i<strlen($mode);$i++) {
					if (count($dynamic_chans[$chan]->nick)>0) {
						$current_nick=trim($nicks[$nv]);
						if (array_key_exists($current_nick,$dynamic_chans[$chan]->nick)) $nick_mode=$dynamic_chans[$chan]->nick[$current_nick];
					}
					switch ($mode[$i]) {
						case "-": $give=false; break;
						case "+":	$give=true; break;
						case "o": if ($nick_mode!="+" and !$give) $dynamic_chans[$chan]->snm($current_nick,"n"); elseif ($give) $dynamic_chans[$chan]->snm($current_nick,"@"); $nv++; break;
						case "h": if (!$give) $dynamic_chans[$chan]->snm($current_nick,"n"); else $dynamic_chans[$chan]->snm($current_nick,"%"); $nv++; break;
						case "v": if ($nick_mode!="@" and $nick_mode!="%") {
								if (!$give) $dynamic_chans[$chan]->snm($current_nick,'n');
								else $dynamic_chans[$chan]->snm($current_nick,'+');
							}
							$nv++;
							break;
						case 'b': if (xreg($bot['nick'].'*!*@'.$bot['host'],$current_nick) && $give && ($dynamic_chans[$chan]->nick[$bot["nick"]]=="@" || $dynamic_chans[$chan]->nick[$bot["nick"]]=='%')) sts('MODE '.$chan.' -b '.$current_nick);
						case "e":
						case "I": break;
						default: $dynamic_chans[$chan]->scm($give.$mode[$i]); break;
					}
				}
				unset($nv,$mode,$give,$nicks);
				putlog($chan,trim(implode(" ",array_values($text))),'IMODE');
			}
		break;
		case "TOPIC":
			if ($text[3]!=":") {
				$other=implode(" ",array_values(array_slice($text,3)));
				$other=str_replace(":","",trim($other));
				$dynamic_chans[$text[2]]->topic=$other;
				putlog($text[2],$other,'ITOPIC');
			}
		break;
		case "NICK":
			$newnick=str_replace(":","",trim($text[2]));
			foreach($dynamic_chans as $chan_local => &$obj) {
				if (array_key_exists($nick,$obj->nick)) {
					$obj->nick[$newnick]=$obj->nick[$nick];
					unset($dynamic_chans[$chan_local]->nick[$nick]);
					putlog($chan_local,$newnick,'INICK');
				}
			}
			if (is_me($nick)) $bot["nick"]=$newnick;
		break;
		case "QUIT":
			foreach($dynamic_chans as $chan_local => &$obj) {
				if (array_key_exists($nick,$obj->nick)) {
					unset($dynamic_chans[$chan_local]->nick[$nick]);
					putlog($chan_local,'IQUIT');
				}
			}
		break;
		case "INVITE":
			if ($join_on_invite && !array_key_exists($target,$dynamic_chans)) sts("JOIN ".trim($text[2]));
		break;
	}
	if (isset($other)) unset($other);
	### Bind calling system
	$action=strtolower($command);
	$text=explode(' ',$raw);
	if (array_key_exists($action,$binds)) {
		if (is_numeric($action)) foreach($binds[$action] as $func) {
			if (function_exists($func)) $func($text);
			else echo("Warning: Function '".$func."' binded on raw #".$command." doesn't exist!");
		}
		else {
			$target=trim($text[2]);
			$target=str_replace(':','',$target);
			$text=array_slice($text,3);
			if ($command=='QUIT') $args_string=$target.' '.implode(' ',$text);
			else {
				$text[0]=substr($text[0],1);
				$args_string=implode(' ',$text);
			}
			if ($command=='PRIVMSG') {
				if (ord($args_string[0])==1 && ord(substr($args_string,-1))==1) {
					$args_string=substr($args_string,1,-1);
					$args=explode(' ',$args_string);
					if ($args[0]=='ACTION') {
						$action='action';
						$args_string=substr($args_string,7);
					}
					else $action='ctcp';
				}
				else $action='privmsg';
			}
			elseif ($command=='NOTICE') {
				if (ord($args_string[0])==1 && ord(substr($args_string,-1))==1) {
					$args_string=substr($args_string,1,-1);
					$action='ctcp-answer';
				}
				else $action='notice';
			}
			$args=explode(' ',$args_string);
			$func_args[0]=$nick;
			for($i=0;$i<count($bind_args[$action]);$i++) {
				$temp=$bind_args[$action][$i];
				if (isset($$temp)) $func_arg=$$temp;
				elseif (is_numeric($temp)) if ($i==count($bind_args[$action])-1) $func_arg=implode(' ',array_slice($args,$temp)); else $func_arg=$args[$temp];
				else echo("Wrong bind argument ".$temp);
				array_push($func_args,$func_arg);
			}
			foreach($binds[$action] as $func) {
				if (function_exists($func)) call_user_func_array($func,$func_args);
				else echo("Warning: Function '".$func."' binded on action '".$action."' doesn't exist!");
			}
			unset($func,$func_arg,$func_args,$args,$args_string,$temp);
		}
	}

}
?>