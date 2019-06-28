<?
if (is_bot_oper($nih)) {
	if (count($arg)==0) stp($target,$nick.": argument is void.");
	else {
		$other=implode(" ",$arg);
		$topic_local=$dynamic_chans[$target]->topic;
		$status_local=$dynamic_chans[$target]->nick[$bot_name];
		if ($status_local!="@" and $status_local!="%") stp($target,$nick.": I'm not a channel operator");
		else sts("topic ".$target." :".$topic_local.$other);
	}
}
else stp($target,$nick.": Access denied");
?>