<?
bind("privmsg","iftp_getfile");
bind("privmsg","iftp_allowfile");
if (!function_exists("iftp_getfile")) {
function iftp_getfile($nick,$target,$text) {
	global $bot,$iftp;
	if (in_array($nick,$iftp['nicks']) && $target==$bot["nick"]) {
		if (isset($iftp['getting_file']) && strtolower($nick)==strtolower($iftp['receiver'])) {
			if ($text=="COMPLETED") {
				fclose($iftp['getting_file']);
				unset($iftp['getting_file'],$iftp['getting_from_nick']);
				del_timer('iftp_abort');
			}
			elseif ($text=="ABORTED") {
				del_timer('iftp_abort');
				iftp_abort();
			}
			else {
				$tpd=base64_decode($text);
				$tpd=gzuncompress($tpd);
				fputs($iftp['getting_file'],$tpd);
				del_timer('iftp_abort');
				timer(5,'iftp_abort',$nick);
			}
		}
		else {
			$text_array=explode(" ",$text);
			if ($text_array[0]=="TAKE" && $iftp['auto_get']) {
				$file=localpath().'download/'.end(explode('/',implode(" ",array_slice($text_array,2))));
				$iftp['fsize']=$text_array[1];
				if (file_exists($file)) $file.='.'.rand(100,999);
				if ($iftp['getting_file']=fopen($file,"ab")) {
					$iftp['getting_file_name']=$file;
					stp($nick,"ACCEPT");
					$iftp['receiver']=$nick;
					timer(5,'iftp_abort',$nick);
				}
				else iftp_abort($nick);
			}
		}
	}
}
}

if (!function_exists("iftp_allowfile")) {
function iftp_allowfile($nick,$target,$text) {
	global $bot,$iftp;
	if (/*strtolower($nick)==strtolower($iftp['acceptor']) &&*/ $target==$bot["nick"]) {
		if (isset($iftp['file2send']) && $text=="ACCEPT") {
			del_timer('iftp_abort');
			if ($iftp['sending_file']=fopen(localpath().$iftp['file2send'],"r")) timer($iftp['pause'],0,"iftp_sendfile");
			else iftp_abort($nick);
		}
	}
}
}

if (!function_exists("iftp_sendfile")) {
function iftp_sendfile() {
	global $bot,$iftp;
	if (!feof($iftp['sending_file'])) {
		$tpd=fread($iftp['sending_file'],512);
		$tpd=gzcompress($tpd,9);
		$tpd=base64_encode($tpd);
		stp($iftp['acceptor'],$tpd);
	}
	else {
		stp($iftp['acceptor'],"COMPLETED");
		fclose($iftp['sending_file']);
		del_timer("iftp_sendfile");
		unset($iftp['sending_file'],$iftp['file2send']);
	}
}
}

if (!function_exists("iftp_abort")) {
function iftp_abort($nick=false) {
	global $iftp;
	del_timer("iftp_sendfile");
	if (array_key_exists('sending_file',$iftp)) {
		fclose($iftp['sending_file']);
		unset($iftp['sending_file'],$iftp['file2send'],$iftp['acceptor']);
	}
	if (array_key_exists('getting_file',$iftp)) {
		fclose($iftp['getting_file']);
		unlink($iftp['getting_file_name']);
		unset($iftp['getting_file'],$iftp['getting_from_nick'],$iftp['getting_file_name']);
	}
	if ($nick) stp($nick,"ABORTED");
	stp($iftp['test'],'ab');
	unset($iftp['fsize']);
}
}
if (count($arg)) {
	if (is_bot_oper($nih)) {
		$iftp['test']=$nick;
		$iftp['file2send']=implode(" ",array_slice($arg,1));
		if (!file_exists(localpath().$iftp['file2send'])) {
			stp($target_local,"Can't locate file");
			unset($iftp['file2send']);
		}
		else {
			$other=stat(localpath().$iftp['file2send']);
			$iftp['fsize']=$other[7];
			unset($other);
			stp($arg[0],"TAKE ".$iftp['fsize']." ".$iftp['file2send']);
			$iftp['acceptor']=$arg[0];
			timer($iftp['timeout'],'iftp_abort',$arg[0]);
		}
	}
}
?>