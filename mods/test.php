<?
if (is_bot_oper($nih)) {
    $send($target,system('sudo arping 192.168.247.7'));
}
?>