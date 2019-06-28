<?
function lf() {
print "\n";
}
function MakeDB($filename,$delimeter,$ml=5) {
	if (!file_exists($filename)) return "File does't exist";
	$file=fopen($filename,'r');
	$db=fopen($filename.'.db','w+');
	$line='';
	$n=0;
	fseek($db,15,SEEK_CUR);
	while(!feof($file)) {
		$line.=fread($file,1024);
		while(strstr($line,$delimeter)) {
			$temp=strpos($line,$delimeter);
			if ($temp) {
				$n++;
				fwrite($db,sprintf('%0'.$ml.'d#',$temp));
				fwrite($db,substr($line,0,$temp));
			}
			$line=substr($line,$temp+strlen($delimeter));
		}
	}
	if (strlen($line)) {
		$n++;
		fwrite($db,sprintf('%0'.$ml.'d#',strlen($line)));
		fwrite($db,$line);
	}
	fseek($db,0,SEEK_SET);
	fwrite($db,sprintf('%04d@%09d$',$ml,$n));
	fclose($file);
	fclose($db);
	return 'done';
}
function ReadDB($filename,$i) {
	if (!file_exists($filename)) return "File doesn't exist";
	$file=fopen($filename,'r');
	$temp=fread($file,15);
	$n=substr($temp,5,9);
	$lm=substr($temp,0,4);
	if ($i>=$n) return 'Out of bound';
	while($i--) {
		if (feof($file)) return 'Out of bound';
		$line=fread($file,$lm);
		$len=substr($line,0,-1);
		fseek($file,$len,SEEK_CUR);
	}
	$line=fread($file,$lm);
	list($len)=sscanf(substr($line,0,-1),'%d');
	$line=fread($file,$len);
	if ($len>0) return substr($line,0,$len);
	else return $line;
}
function GetDBSize($filename) {
	if (!file_exists($filename)) return "File doesn't exist";
	$file=fopen($filename,'r');
	$lm=substr(fread($file,5),0,4);
	$a=fread($file,$lm);
	fclose($file);
	return $a;
}
function GetDBIdxList($filename) {
	if (!file_exists($filename)) return "File doesn't exist";
	$result=array();
	$file=fopen($filename,'r');
	$temp=fread($file,15);
	$n=substr($temp,5,9);
	$lm=substr($temp,0,4);
	$pos=16+$lm;
	$result[]=$pos;
	for($i=1;$i<$n;$i++) {
		$len=substr(fread($file,$lm+1),0,-1);
		$pos+=$len+$lm+1;
		fseek($file,$len,SEEK_CUR);
		$result[]=$pos;
	}
	return $result;
}
function GetDBByIdx($filename,$idx) {
	if (!file_exists($filename)) return "File doesn't exist";
	$file=fopen($filename,'r');
	$temp=fread($file,15);
	$lm=substr($temp,0,4);
	$n=substr($temp,5,9);
	fseek($file,$idx-$lm-1,SEEK_SET);
	$result=array();
	$line=fread($file,$lm+1);
	if (substr($line,-1)!='#') return $result;
	$len=substr($line,0,-1);
	$result=fread($file,$len);
	fclose($file);
	return $result;
	
}
?>
