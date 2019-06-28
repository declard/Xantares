<?
if (!class_exists('buktopuha_user')) {
class buktopuha_user { var $scores,$count,$max,$time; }
}

if (!function_exists('buktopuha_load_stat')) {
function buktopuha_load_stat(&$array) {
	$fb=fopen(localpath()."data/vik_stat.dat","r");
	while (!feof($fb)) {
		$other=explode(" ",trim(fgets($fb)));
		$array[$other[0]]=new buktopuha_user;
		$array[$other[0]]->count=$other[2];
		$array[$other[0]]->scores=$other[1];
		$array[$other[0]]->time=$other[4];
		$array[$other[0]]->max=$other[3];
	}
	fclose($fb);
}
}

if (!function_exists("buktopuha_save_stat")) {
function buktopuha_save_stat() {
	global $buktopuha;
	$fb=fopen(localpath()."data/vik_stat.dat","w");
	$lf=false;
	foreach($buktopuha['stat'] as $nick => $stat) {
		if ($lf) fwrite($fb,"\n");
		$lf=true;
		if (is_object($stat))
			if (strlen($other=$nick." ".$stat->scores." ".$stat->count." ".$stat->max." ".$stat->time)>2) fwrite($fb,trim($other));
	}
	fclose($fb);
}
}

if (!function_exists("buktopuha_put_stat")) {
function buktopuha_put_stat($nick,$time,$scores) {
	global $buktopuha;
	$mnick=&$buktopuha['max_nick'];
	if ($mnick==$nick) $buktopuha['max_num']++;
	else {
		$buktopuha['max_nick']=$nick;
		$buktopuha['max_num']=1;
	}
	if (!isset($buktopuha['stat'][$nick])) {
		$buktopuha['stat'][$nick]=new buktopuha_user;
		$buktopuha['stat'][$nick]->count = 0;
		$buktopuha['stat'][$nick]->scores = 0;
		$buktopuha['stat'][$nick]->time = null;
		$buktopuha['stat'][$nick]->max = 1;
	}
	$buktopuha['stat'][$nick]->count++;
	$buktopuha['stat'][$nick]->scores+=$scores;
	$prevtime=&$buktopuha['stat'][$nick]->time;
	if ($prevtime>$time || !is_numeric($prevtime)) {
		if (is_numeric($prevtime)) stp($buktopuha['chan'],"03Это твой самый быстрый ответ");
		$prevtime=$time;
	}
	$prevmax=&$buktopuha['stat'][$nick]->max;
	if ($mnick==$nick && $buktopuha['max_num']>$prevmax) $prevmax++;
	if ($buktopuha['max_num']==3) stc($buktopuha['chan'],"ACTION выдал 04".$nick." грамату пач0тнава знатака за 043 ответа подряд!");
	if ($buktopuha['max_num']==5) stc($buktopuha['chan'],"ACTION выдал 04".$nick." мидаль мегапач0тнава знатака за 045 ответов подряд!");
	if ($buktopuha['max_num']==10) stc($buktopuha['chan'],"ACTION выдал 04".$nick." орденЪ б/птрупач0тнава знатака за аж целых 040xA ответов подряд!!");
}
}

if (!function_exists("buktopuha_get_answer")) {
function buktopuha_get_answer($nick,$chan,$text) {
	global $buktopuha;
	if ($chan==$buktopuha["chan"] && array_key_exists('answer',$buktopuha)) {
		if (trim(strtolower($text))==trim(strtolower($buktopuha["answer"]))) {
			$time=round(microtime(true)-$buktopuha["time"],2);
			$count=3-$buktopuha["hints"];
			$congrat="03Молодец, 04".$nick."03! Правильный ответ 04".$buktopuha["answer"]."03 был дан за 04".$time."03 сек. Ты получаешь 04".$count."03 очка";
			if ($buktopuha['count']) stp($chan,$congrat."! Следующий вопрос через 04".$buktopuha["next"]."03 секунд.");
			else stp($chan,$congrat);
			buktopuha_put_stat($nick,$time,$count);
			del_timer("buktopuha_no_answer");
			buktopuha_ask_question();
		}
		elseif (strtolower($text)=="!х" && $buktopuha["hints"]<2 && $buktopuha["hints"]<=strlen($buktopuha["answer"])-2) stp($chan,"04".substr($buktopuha["answer"],0,++$buktopuha["hints"]));
	}
}
}

if (!function_exists("buktopuha_no_answer")) {
function buktopuha_no_answer() {
	global $buktopuha;
	$chan=$buktopuha["chan"];
	$msg="03Никто не ответил на вопрос. Ответ не узнает никто.";
	if ($buktopuha['count']) stp($buktopuha['chan'],$msg." Следующий вопрос через 04".$buktopuha["next"]."03 сек.");
	else stp($buktopuha['chan'],$msg);
	buktopuha_ask_question();
}
}

if (!function_exists("buktopuha_ask_question")) {
function buktopuha_ask_question($immed=false) {
	global $buktopuha;
	unbind("privmsg","buktopuha_get_answer");
	unset($buktopuha['answer']);
	if ($buktopuha['count']) {
		$array_question=array_splice($buktopuha["file"],rand(0,--$buktopuha["count"]),1);
		$question=explode("::",$array_question[0]);
		$buktopuha["question"]=$question[0];
		$buktopuha["answer"]=trim($question[1]);
		$buktopuha['hints']=0;
		if (!$immed) timer($buktopuha['next'],"buktopuha_question");
		else buktopuha_question();
	}
	else {
		stp($buktopuha["chan"],"03Вопросы в базе кончились. Викторина остановлена.");
		buktopuha_save_stat();
		unset($GLOBALS['buktopuha']);
	}
}
}

if (!function_exists("buktopuha_question")) {
function buktopuha_question() {
	global $buktopuha;
	timer($buktopuha['wait'],"buktopuha_no_answer");
	stp($buktopuha['chan'],"02".$buktopuha["question"]);
	$buktopuha['time']=microtime(true);
	bind("privmsg","buktopuha_get_answer");
}
}

if ($target_local[0]=="#") {
	if (count($arg)) {
		$status_remote_local=$dynamic_chans[$target]->nick[$nick];
		if ($arg[0]=="старт") {
			if ($status_remote_local!="%" && $status_remote_local!="@") stp($target_local,"04Только (полу-)операторы могут запускать викторину.");
			elseif (isset($buktopuha)) stp($target_local,"04Викторина уже запущена.");
			elseif (!file_exists(localpath()."data/vik.dat")) stp($target_local,"04База вопросов не найдена!");
			else {
				$buktopuha=array();
				$buktopuha['file']=file(localpath()."data/vik.dat");
				$buktopuha['count']=count($buktopuha["file"]);
				$buktopuha['wait']=60;
				$buktopuha['chan']=$target_local;
				$buktopuha['next']=5;
				$buktopuha['hints']=0;
				$buktopuha['max_nick']=null;
				$buktopuha['max_num']=0;
				$buktopuha['time']=microtime(true);
				if (!file_exists(localpath()."data/vik_stat.dat")) {
					stp($target_local,"04Внимание, база пользователей не найдена.");
					$buktopuha['stat']=array();
				}
				else {
					buktopuha_load_stat($buktopuha['stat']);
					array_multisort($buktopuha['stat'],SORT_DESC,SORT_REGULAR);
				}
				stp($target_local,"03На канале запущена викторина. Текущая база содержит 04".$buktopuha["count"]."03 вопросов. Первый вопрос:");
				buktopuha_ask_question(true);
			}
		}
		elseif ($arg[0]=="стоп") {
			if ($status_remote_local!="%" && $status_remote_local!="@") stp($target_local,"04Только (полу-)операторы могут останавливать викторину.");
			elseif (!isset($buktopuha)) stp($target_local,"04Викторина не запущена.");
			else {
				buktopuha_save_stat();
				unbind("privmsg","buktopuha_get_answer");
				unset($buktopuha);
				del_timer("buktopuha_no_answer");
				del_timer("buktopuha_question");
				stp($target_local,"04Викторина остановлена");
			}
		}
		elseif ($arg[0]=="стат") {
			if (!isset($buktopuha)) {
				if (file_exists(localpath()."data/vik_stat.dat")) {
					buktopuha_load_stat($buktopuha['stat']);
					array_multisort($buktopuha['stat'],SORT_DESC,SORT_REGULAR);
					$buktopuha['not_started']=true;
					if (!array_key_exists($nick,$buktopuha['stat'])) stp($target_local,"04Вы не участвовали в игре.");
					else stp($target_local,"03Вы ответили на 04".$buktopuha['stat'][$nick]->count."03 вопросов, колличество очков: 04".$buktopuha['stat'][$nick]->scores."03, минимальное время: 04".$buktopuha['stat'][$nick]->time."03 сек., максимальное кол-во ответов подряд: 04".$buktopuha['stat'][$nick]->max);
					unset($buktopuha);
				}
				else stp($target_local,"04База пользователей отсутствует");
			}
			else {
				if (!array_key_exists($nick,$buktopuha['stat'])) stp($target_local,"04Вы не участвовали в игре.");
				else stp($target_local,"03Вы ответили на 04".$buktopuha['stat'][$nick]->count."03 вопросов, колличество очков: 04".$buktopuha['stat'][$nick]->scores."03, минимальное время: 04".$buktopuha['stat'][$nick]->time."03 сек., максимальное кол-во ответов подряд: 04".$buktopuha['stat'][$nick]->max);
			}
		}
		elseif ($arg[0]=="место") {
			if (!file_exists(localpath()."data/vik_stat.dat") && !isset($buktopuha)) stp($target_local,"04База пользователей отсутствует");
			elseif (!isset($buktopuha)) {
				buktopuha_load_stat($buktopuha['stat']);
				array_multisort($buktopuha['stat'],SORT_DESC,SORT_REGULAR);
				$buktopuha['not_started']=true;
			}
			else {
				array_multisort($buktopuha['stat'],SORT_DESC,SORT_REGULAR);
				$leaders=array_slice($buktopuha['stat'],0,5);
				$leaders_list='';
				$dot=false;
				foreach($leaders as $key => $obj) {
					if ($dot) $leaders_list.=", ";
					$dot=true;
					$leaders_list.=$key." (".$obj->scores.")";
				}
				$string="03Текущие лидеры: 04".$leaders_list;
				if (array_key_exists($nick,$buktopuha['stat'])) {
					$pos=array();
					$i=1;
					foreach($buktopuha['stat'] as $key => $obj) $pos[$key]=$i++;
					stp($target_local,$string."03. Вы занимаете 04".$pos[$nick]." 04место");
				}
				else stp($target_local,$string."03. Вы ниразу не участвовали в игре.");
				unset($leaders,$leaders_list,$pos,$i,$dot,$string);
				if (array_key_exists('not_started',$buktopuha)) unset($buktopuha);
			}
		}
		 
	}
	else
		if (isset($buktopuha)) stp($target_local,"03Викторина запущена, Текущий вопрос: 04".$buktopuha["question"]);
		else stp($target_local,"03Викторина не запущена");
}
?>