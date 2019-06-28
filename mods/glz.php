<?    
$replace_to=$arg[0];
$other=implode(" ",array_slice($arg,1));
$other=str_replace(array('а','е','ё','и','о','у','э','ю','я'),$replace_to,$other);
stp($target,"Все гласные заменены на ".$replace_to." >> ".$other);   
?>
