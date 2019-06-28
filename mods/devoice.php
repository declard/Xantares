<?
$status_local=$dynamic_chans[$target]->nick[$bot["nick"]];
$status_remote_local=$dynamic_chans[$target]->nick[$nick];
if ($status_local!="%" and $status_local!="@") stp($target,$nick.": I'm not a channel operator");
elseif ($status_remote_local!="v") stp($target,$nick.": u haven't voice");
else sts("mode $target -v $nick");
?>