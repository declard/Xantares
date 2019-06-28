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
		$temp=explode("@",strtolower($temp[1]));
		$ident=trim($temp[0]);
		$host=trim($temp[1]);
	}
	else $nick=$nih;
	$command=trim($text[1]);
	$command=ereg_replace(":","",$command);
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
			if (array_key_exists(353,$binds)) foreach($binds[353] as $func) {
				if (function_exists($func)) $func($text);
				else echo("Warning: Binded on \'$command\' function \'$func\' not found!");
			}
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
				if ($other[0]=="ACTION") putlog($target_local,"[","] * ".$nick." ".implode(" ",array_slice($other,1)));
				else putlog($target_local,"[","] [".$nick." ".$other[0]."] ".implode(" ",array_slice($other,1)));
				unset($other);
			}
			else if ($target!=$bot["nick"]) putlog($target_local,"[","] <".$nick."> ".$text_line);
			else putlog($target_local,"[","] *".$nick."* ".$text_line);
			
			if (array_key_exists(strtolower($command),$binds)) foreach($binds[strtolower($command)] as $func) {
				if (function_exists($func)) $func($nick,$target,$text_line);
				else echo("Warning: Binded on \'$command\' function \'$func\' not found!");
			}
			### Реакция на ник бота
			//if (strstr(strtolower(strip($text_line,"a")),strtolower($bot["nick"])) /*&& in_array(strtolower($target),$talk_chans) */&& strtolower($nick)!="хентай") include(localpath()."mods/fraza.php");
			### Загрузка модулей
			
			if (strlen($text[0])&&substr($text[0],0,1)==$cmd_char&&$text[0][1]!="") {
				$inc_module=strip(translit(substr($text[0],1)),"a");
				$exceptions=array("/",".","\\","con","aux","|",">","<");
				if (strfind($inc_module,"/",".","\\","con","aux","|",">","<")) time(); #stp($target,$nick.": access denied :E");
				else {
					$inc_module=localpath()."mods/".trim($inc_module).".php";
					if (file_exists($inc_module)) {
						$arg=array_values(array_slice($text,1));
						if ($target==$bot["nick"]) $target_local=$nick;
						else $target_local=$target;
						$send='stp';
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
				switch (strtolower(str_replace("","",$text[0]))) {
				case "version":
							stca($nick,"VERSION PHP bot v".$bot['version'].". Written by Tamahome and XapoH");
							break;
				case "ping":
							stca($nick,"PING ".preg_replace("/\x01/","",$text[1]));
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
			if ($target[0]=="#") putlog($target,"[","] -".$nick."- ".$text_line);
			else putlog("_PRIVAT","[","] -".$nick."- ".$text_line);
			if (array_key_exists(strtolower($command),$binds)) foreach($binds[strtolower($command)] as $func) {
				if (function_exists($func)) $func($nick,$target,$text_line);
				else echo("Warning: Binded on \'$command\' function \'$func\' not found!");
			}
		break;
		case "JOIN":
			$target=trim($text[2]);
			$target=str_replace(":","",$target);
			if (strtolower($nick)!=strtolower($bot["nick"])) {
				putlog($target,"[","] *** $nick has joined ".$target);
				$dynamic_chans[$target]->nick[$nick]="n";
				if ($greet_on_join && $nick!="ChanServ" && $nick!="SeenServ" && $nick!="hentai") { sleep(rand(2,5)); stp($target,"Привет, $nick"); }
			}
			else {
				if (!array_key_exists(strtolower($target),array_change_key_case($dynamic_chans))) $dynamic_chans[$target]=new chan;
				sts("mode ".$target);
				putlog($target,"[","] *** ".$target." has been joined.");
			}
			if (array_key_exists(strtolower($command),$binds)) foreach($binds[strtolower($command)] as $func) {
				if (function_exists($func)) $func($nick,$target);
				else echo("Warning: Binded on \'$command\' function \'$func\' not found!");
			}
		break;
		case "PART":
			$target=trim($text[2]);
			$target=str_replace(":","",$target);
			$text=array_values(array_slice($text,3));
			if (count($text)!=0) {
				$text[0]=str_replace(":","",$text[0]);
				$other=array_pop($text);
				$other=trim($other);
				array_push($text,$other);
				$comment=" (".implode(" ",$text).")";
			}
			
			else $comment=".";
			if (strtolower($nick)!=strtolower($bot["nick"])) {
				putlog($target,"[","] *** $nick has part ".$target.$comment);
				$nv=array_search(strtolower($nick),array_keys(array_change_key_case($dynamic_chans[$target]->nick)));
				array_splice($dynamic_chans[$target]->nick,$nv,1);
			}
			else {
				putlog($target,"[","] *** ".$target." has been parted.");
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
				$other=array_pop($text);
				$other=trim($other);
				array_push($text,$other);
				$comment=" (".implode(" ",$text).")";
			}
			else $comment=".";
			if (array_key_exists(strtolower($command),$binds)) foreach($binds[strtolower($command)] as $func) {
				if (function_exists($func)) $func($nick,$target,$target_nick,$comment);
				else echo("Warning: Binded on \'$command\' function \'$func\' not found!");
			}
			if (strtolower($target_nick)!=strtolower($bot["nick"])) {
				putlog($target,"[","] *** $target_nick has been kicked by ".$nick.$comment);
				$nv=array_search(strtolower($target_nick),array_keys(array_change_key_case($dynamic_chans[$target]->nick)));
				array_splice($dynamic_chans[$target]->nick,$nv,1);
			}
			else {
				putlog($target,"[","] *** I'v been kicked by ".$nick.$comment);
				unset($dynamic_chans[$target]);
				if ($join_after_kick) {
					putlog($target,"[","] *** Attempt to rejoin...");
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
						case "-": $give="-"; break;
						case "+":	$give="+"; break;
						case "o": if ($nick_mode!="+" and $give=="-") $dynamic_chans[$chan]->snm($current_nick,"n"); elseif ($give=="+") $dynamic_chans[$chan]->snm($current_nick,"@"); $nv++; break;
						case "h": if ($give=="-") $dynamic_chans[$chan]->snm($current_nick,"n"); else $dynamic_chans[$chan]->snm($current_nick,"%"); $nv++; break;
						case "v": if ($nick_mode!="@" and $nick_mode!="%") {
								if ($give=="-") $dynamic_chans[$chan]->snm($current_nick,"n");
								else $dynamic_chans[$chan]->snm($current_nick,"+");
							}
							$nv++;
							break;
						case "b": if ((strstr(strtolower($current_nick),strtolower($bot["nick"])) || strstr($current_nick,$bot["host"])) && $give=="+" && $dynamic_chans[$chan]->nick[$bot["nick"]]=="@") sts("MODE ".$chan." -b ".$current_nick);
						case "e":
						case "I": break;
						default: $dynamic_chans[$chan]->scm($give.$mode[$i]); break;
					}
				}
				unset($nv,$mode,$give,$nicks);
				putlog($chan,"[","] *** ".$nick." sets mode: ".trim(implode(" ",array_values($text))));
			}
		break;
		case "TOPIC":
			if ($text[3]!=":") {
				$other=implode(" ",array_values(array_slice($text,3)));
				$other=str_replace(":","",$other);
				$dynamic_chans[$text[2]]->topic=$other;
				putlog($text[2],"[","] *** ".$nick." изменил топик на ".rtrim($other));
			}
		break;
		case "NICK":
			$newnick=str_replace(":","",trim($text[2]));
			foreach(array_keys($dynamic_chans) as $chan_local) {
				if (array_search($nick,array_keys($dynamic_chans[$chan_local]->nick))) {
					$dynamic_chans[$chan_local]->nick[$newnick]=$dynamic_chans[$chan_local]->nick[$nick];
					$nv=array_search($nick,array_keys($dynamic_chans[$chan_local]->nick));
					array_splice($dynamic_chans[$chan_local]->nick,$nv,1);
					putlog($chan_local,"[","] *** ".$nick." меняет ник на ".$newnick);
				}
			}
			if ($nick==$bot["nick"]) $bot["nick"]=$newnick;
			
			if (array_key_exists(strtolower($command),$binds)) foreach($binds[strtolower($command)] as $func) {
				if (function_exists($func)) $func($nick,$newnick);
				else echo("Warning: Binded on \'$command\' function \'$func\' not found!");
			}
			unset($newnick,$nv);
		break;
		case "QUIT":
			foreach(array_keys($dynamic_chans) as $chan_local) {
				if (array_search($nick,array_keys($dynamic_chans[$chan_local]->nick))) {
					$nv=array_search($nick,array_keys($dynamic_chans[$chan_local]->nick));
					array_splice($dynamic_chans[$chan_local]->nick,$nv,1);
					putlog($chan_local,"[","] *** Вышел: ".$nick.".");
				}
			}
		break;
		case "INVITE":
			if ($join_on_invite && !array_key_exists($target,$dynamic_chans)) sts("JOIN ".trim($text[2]));
		break;
	}
	if (isset($other)) unset($other);
}
?>