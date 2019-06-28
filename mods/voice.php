<?
if (is_bot_oper($nih)) {
	$status_local=$dynamic_chans[$target]->nick[$bot["nick"]];
	$status_remote_local=$dynamic_chans[$target]->nick[$nick];
	if ($status_local!="%" and $status_local!="@") stp($target,$nick.": I'm not a channel operator");
	elseif ($status_remote_local=="+") stp($target,$nick.": u already have halfoperator");
	else sts("mode $target +v $nick");
}
else stp($target,$nick.": Access denied");
?>