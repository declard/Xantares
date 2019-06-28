<?
function Xstrtolower($str) {
	return strtr($str,
		'ÀÁÂÃÄÅ¨ÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞ‗ABCDEFGHIJKLMNOPQRSTUVWXYZ',
		'אבגדהו¸זחטיךכלםמןנסעףפץצקרשתûü‎‏ÿabcdefghijklmnopqrstuvwxyz');
}
function Xstrtoupper($str) {
	return strtr($str,
		'אבגדהו¸זחטיךכלםמןנסעףפץצקרשתûü‎‏ÿabcdefghijklmnopqrstuvwxyz',
		'ÀÁÂÃÄÅ¨ÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞ‗ABCDEFGHIJKLMNOPQRSTUVWXYZ');
}
function Xarray_change_key_case($input,$c=CASE_LOWER) {
	if ($c==CASE_LOWER) for($i=0;$i<count($input);$i++) $input[$i]=Xstrtolower($input[$i]);
	else if ($c==CASE_UPPER) for($i=0;$i<count($input);$i++) $input[$i]=Xstrtoupper($input[$i]);
	return $input;
}
?>