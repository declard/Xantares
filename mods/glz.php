<?    
$replace_to=$arg[0];
$other=implode(" ",array_slice($arg,1));
$other=str_replace(array('�','�','�','�','�','�','�','�','�'),$replace_to,$other);
stp($target,"��� ������� �������� �� ".$replace_to." >> ".$other);   
?>
