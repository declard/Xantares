<?
if (count($arg)) $send($target_local,'Хэшировано: '.md5(implode(' ',$arg)));
?>