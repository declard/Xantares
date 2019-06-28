<?
if (!function_exists("vote")) {
function vote($nick,$target,$reply) {
	global $bot,$vote,$vote_users;
	$reply=trim($reply);
	if ($target!=$bot["nick"]) return;
	if (isset($vote)) {
		if (array_key_exists($nick,$vote_users)) {
			if ($reply=="yes" || $reply=="да") {
				stn($nick,"Ваш ответ: Да. Вы можете изменить его до окончания опроса.");
				$vote_users[$nick]="yes";
			}
			elseif ($reply=="no" || $reply=="нет") {
				stn($nick,"Ваш ответ: Нет. Вы можете изменить его до окончания опроса.");
				$vote_users[$nick]="no";
			}
		}
		else stn($nick,"У Вас нет права голоса на текущем опросе.");
	}
}
}

if (!function_exists("vote_nick")) {
function vote_nick($nick,$newnick) {
	global $vote_users;
	if (isset($vote_users)) if (array_key_exists($nick,$vote_users)) {
		$vote_users[$newnick]=$vote_users[$nick];
		unset($vote_users[$nick]);
	}
}
}

if (!function_exists("vote_result")) {
function vote_result($target) {
	global $vote,$vote_users;
	$result_yes=$result_no=$result_noreply=$users_count=0;
	foreach ($vote_users as $user => $answer) {
		if ($answer == "yes") $result_yes++;
		elseif ($answer == "no") $result_no++;
		else $result_noreply++;
		$users_count++;
	}
	stp($target,"Опрос завершен.");
	stp($target,"Тема опроса: ".$vote["question"]);
	stp($target,"За: $result_yes. Против: $result_no. Не голосовало: $result_noreply.");
	unset($GLOBALS["vote"],$GLOBALS["vote_users"]);
}
}

if ($target_local[0]=="#") {
	$status_remote_local=$dynamic_chans[$target]->nick[$nick];
	if ($status_remote_local!="@" and $status_remote_local!="%") stn($nick,"Только операторы и полуоператоры канала могут проводить опросы.");
	elseif (isset($vote)) stn($nick,"Извините, в данный момент система опросов уже задействована. Пожалуйста повторите команду позже.");
	elseif (!count($arg)) stn($nick,"Вы не указали вопрос.");
	else {
		bind("nick","vote_nick");
		bind("privmsg","vote");
		$vote["question"]=implode(" ",$arg);
		foreach ($dynamic_chans[$target]->nick as $nick_local => $status) {
			if ($nick_local!=$bot["nick"]) $vote_users[$nick_local]="";
		}
		stn($target,"Внимание: На канале начинается опрос.");
		stp($target,"На канале запущен опрос.");
		stp($target,"Тема опроса: ".$vote["question"]);
		stp($target,"Подача голосов: /msg ".$bot["nick"]." yes или /msg ".$bot["nick"]." no. Итоги будут подведены через 3 минуты.");
		timer(180,"vote_result",$target);
	}
}
?>