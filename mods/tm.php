<?
if (count($arg)) {
	$dtape=array();
	if ($arg[0][0]=='/') {
		if ($arg[0][1]=='c') $type='c';
		$arg=array_slice($arg,1);
	}
	else $type='n';
	$ctape=implode('',$arg);
	$pos=$a=0;
	$cs=$ce=array();
	$out='';
	for($i=0;$i<strlen($ctape);$i++) {
		switch($c=$ctape[$i]) {
			case '.': if (!array_key_exists($pos,$dtape)) $dtape[$pos]=0; if ($type=='n') $out.=' '.$dtape[$pos]; else $out.=chr($dtape[$pos]); break;
			case '>': $pos++; break;
			case '<': $pos--; break;
			case '+': if (!array_key_exists($pos,$dtape)) $dtape[$pos]=0; $dtape[$pos]++; break;
			case '-': if (!array_key_exists($pos,$dtape)) $dtape[$pos]=0; $dtape[$pos]--; break;
			case '[': array_push($cs,$i); break;
			case ']': if (!array_key_exists($pos,$dtape)) $dtape[$pos]=0; if (!$dtape[$pos]) array_pop($cs); else array_push($cs,$i=array_pop($cs)); break;
			default: if (!array_key_exists($pos,$dtape)) $dtape[$pos]=0; if (is_numeric($ctape[$i])) $dtape[$pos]+=intval($ctape[$i]); else $i=100000; break;
		}
		//if ($c=='+'||$c=='-') print implode(' ',$dtape)."\n";
		if ($a++>100000) break;
	}
	if (strlen($out)<100) if (is_bot_oper($nih)) stp($target_local,$out); else $send($nick,$out);
	//print "\n";
}
// цифра[>цифра[>+>+<<-]>>[<<+>>-]<<<-]>. Произведение
// цифра[[>]+[<]>-]>>>>[<<<->+[>]<-<+[<]>>>>]<[<+>-]<<[>>+>+<<<-]>>[<->-]<[>+<-]>.>. Деление с остатком
?>