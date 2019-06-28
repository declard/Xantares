<?
if (!class_exists('weather')) {
class weather {
	var $file,$type,$result=array(),$error,$item;
	function weather($name) {
		$code=null;
		$this->item=false;
		$this->error=$this->type='';
		$city=strtolower($name);
		$city_file=file(localpath().'data/city.dat');
		$p=array();
		for ($i=0; $i<count($city_file); $i++){
			$p=explode("::",$city_file[$i]);
			if (strtolower($p[1])==$city) {
				$code=$p[0];
				break;
			}
		}
		if (!$code) $this->Error("Я не знаю такого города");
		else $this->file='http://informer.gismeteo.ru/rss/'.$code.'.xml';
	}
	function StartElement($parser, $name, $attrs) {
		if ($name=='ITEM') $this->item=true;
		elseif ($this->item)
			if ($name=='DESCRIPTION') $this->type='desc';
			elseif ($name=='TITLE') $this->type='title';
	}
	function EndElement($parser, $name) {
		if ($name=='ITEM') $this->item=false;
		if ($name=='DESCRIPTION'||$name=='TITLE') $this->type='';
	}
	function GetData($parser,$data) {
		if ($this->item) if ($this->type=='title') $this->result[]='04'.substr($data,strpos($data,' ')+1); if ($this->type=='desc') $this->result[count($this->result)-1].=' 03'.$data;
	}
	function Parse() {
		$xml_parser = xml_parser_create();
		xml_set_object($xml_parser,$this);
		xml_set_element_handler($xml_parser,'StartElement','EndElement');
		xml_set_character_data_handler($xml_parser,'GetData');
		if (!($fp = fopen($this->file, "r"))) {
			$this->Error("could not open XML input");
			return;
		}
		while ($data = fread($fp, 4096)) {
			if (!xml_parse($xml_parser, $data, feof($fp))) $this->Error(sprintf("XML error: %s at line %d",xml_error_string(xml_get_error_code($xml_parser)),xml_get_current_line_number($xml_parser)));
		}
		xml_parser_free($xml_parser);
		
	}
	function Error($string) {
		$this->error=$string;
	}
};
function utf8_to_cp1251($fcontents) {
	$out = $c1 = '';
	$byte2 = false;
	for ($c = 0;$c < strlen($fcontents);$c++) {
		$i = ord($fcontents[$c]);
		if ($i <= 127) $out .= $fcontents[$c];
		if ($byte2) {
			$new_c2 = ($c1 & 3) * 64 + ($i & 63);
			$new_c1 = ($c1 >> 2) & 5;
			$new_i = $new_c1 * 256 + $new_c2;
			if ($new_i == 1025) $out_i = 168;
			else if ($new_i == 1105) $out_i = 184; else $out_i = $new_i - 848;
			$out .= chr($out_i);
			$byte2 = false;
		}
		if (($i >> 5) == 6) {
			$c1 = $i;
			$byte2 = true;
		}
	}
	return $out;
}
}

if (!count($arg)) stp($target_local,'Использовать: '.$cmd_char."п \x02имя города\x02");
else {
	$t=new weather(implode(' ',$arg));
	$t->parse();
	if (!$t->error) {
		$send($nick,'03Погода в городе 04'.implode(' ',$arg).':');
		while($temp=array_shift($t->result)) $send($nick,utf8_to_cp1251($temp));
	}
	else $send($nick,$t->error);
	unset($t,$temp);
}

?>