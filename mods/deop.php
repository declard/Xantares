<?
$status_local=$dynamic_chans[$target]->nick[$bot["nick"]];
$status_remote_local=$dynamic_chans[$target]->nick[$nick];
if ($status_local!="@") stp($target,$nick.": I'm not a channel operator");
elseif ($status_remote_local!="@" and $status_remote_local!="%") stp($target,$nick.": u are not operator");
else sts("mode $target -o $nick");
?>