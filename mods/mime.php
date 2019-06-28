<?
if (!function_exists('unescape_string')) {
	function unescape_string($str) {
		$bslash=false;
		$res='';
		for($i=0;$i<strlen($str);$i++) {
			$chr=$str[$i];
			if ($bslash) {
				switch($chr) {
					case 'n': $res.="\n"; break;
					case 't': $res.="\t"; break;
					case 'r': $res.="\r"; break;
					case "\\": $res.=$chr; break;
					default: $res.="\\".$chr;
				}
				$bslash=false;
			}
			elseif ($chr=="\\") $bslash=true;
			else $res.=$chr;
		}
		return $res;
	}
}
$send($target_local,'Encoded: '.base64_encode(unescape_string(implode(' ',$arg))));
?>
