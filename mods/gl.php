<?    
$other=implode(" ",$arg);
$other=str_replace(array('а','е','ё','и','о','у','э','ю','я'),'',$other);
stp($target,"Ваш текст без гласных >> ".$other);   
?>
