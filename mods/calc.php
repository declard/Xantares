<?
if (!class_exists('calc_parser')) {
class calc_parser {
	var $string,$pos,$prevstate,$state,$depth,$res=array(),$onewords,$instances=array('-'=>0,'+'=>0,'*'=>0,'/'=>0,'%'=>0,'^'=>0,'&'=>0,','=>0,'|'=>0,'('=>2,')'=>3),$errpos,$allowed_funcs;
	function calc_parser ($string) {
		$this->string=$string;
		$this->pos=0;
		$this->depth=0;
		$this->prevstate=$this->state=0;
		$this->allowed_funcs=array('abs'=>1,'acos'=>1,'acosh'=>1,'asin'=>1,'asinh'=>1,'atan'=>1,'atanh'=>1,'ceil'=>1,'cos'=>1,'cosh'=>1,'deg2rad'=>1,'exp'=>1,'floor'=>1,'fmod'=>2,'log10'=>1,'log'=>1,'max'=>2,'min'=>2,'pow'=>2,'rad2deg'=>1,'rand'=>2,'round'=>1,'sin'=>1,'sinh'=>1,'sqrt'=>1,'srand'=>1,'tan'=>1,'tanh'=>1);
	}
	function LP() {
		$this->errpos=$this->pos;
		$delimiters=array(" ","\t","\r","\n");
		$automat=array(
		/*
		-1 слово готово, пора возвращать
		0 начало сканирования
		1 получили символ, надо копить пока это символ
		2 получили предопределенное слово из одного символа
		*/
		//состояния  0,  1,  2 
		"0"=>array( 0, -1, -1),//разделитель
		"1"=>array( 2, -1, -1),//слово из одного символа
		"2"=>array( 1,  1, -1),//символ
		);
		$state=0;
		$word="";
		while ($this->pos<strlen($this->string)) {
			$char=$this->string[$this->pos];
			if (in_array($char,$delimiters)) $instate=0;
			elseif (array_key_exists($char,$this->instances)) $instate=1;
			else $instate=2;
			$state=$automat[$instate][$state];
			switch($state) {
				case -1: // слово готово, пора возвращать
				if (strlen($word)) return $word;
				break;
				case 1: // получили символ, надо копить пока это символ
				$word.=$char; 
				break;
				case 2: // получили предопределенное слово из одного символа
				$word=$char;
				break;
			}
			$this->pos++;
			if ($this->pos==strlen($this->string) && strlen($word)) return $word;
		}
		return false;
	}
	function SP() {
		$state=0;
		$n=0;
		$atoms=array();
		//$res=array();
		$func='';
		while (1) {
			$word=$this->LP();
			if ($word===false) {
				if (!$this->depth && !$func && $this->prevstate!=2)
					if (is_array($res=($this->pEval($atoms)))) return implode('',$res);
					else return $res;
				else return "Parse error: unexpected end.";
			}
			$automat=array(
			/*
			-1 error
			0 начат разбор
			1 арг, ожидаеца оператор или пр скобка
			2 оператор, ожидаеца аргумент или функция или л. скобка
			3 левая скобка, ожидаеца минус или левая скобка или число или функция или п скобка
			4 правая скобка, ожидаеца оператор или правая скобка
			5 функция, ожидаеца левая скобка
			*/
			/////0  1  2  3  4  5
			array(2, 2,-1,2, 2,-1),//оператор
			array(1,-1, 1,1,-1,-1),//операнд
			array(3,-1, 3,3,-1, 3),//л скобка
			array(-1,4,-1,4, 4,-1),//п скобка
			array(5,-1, 5,5,-1,-1)//функция
			);
			if (array_key_exists($word,$this->instances)) $state=$this->instances[$word];
			elseif (preg_match("/^[0-9]+(\.[0-9]+)?$/",$word)) $state=1;
			elseif (preg_match("/^0x[0-9A-Fa-f]+$/",$word)) {
				$word=base_convert($word,16,10);
				$state=1;
			}
			elseif (strtolower($word)=='pi') {
				$word='M_PI';
				$state=1;
			}
			elseif (preg_match("/^[_a-zA-Zа-яА-Я]+[_0-9a-zA-Zа-яА-Я]*$/",$word)) $state=4;
			else return "Parse error: wrong atoms sequence (".$this->errpos.")";

			$this->state=$automat[$state][$this->state];
			
			switch($this->state) {
				case -1:	##### Ошибка
				return "Parse error: wrong atoms sequence (".$this->errpos.")";
				case 1:	##### АргХумент
				$atoms[$n++]=$word;
				break;
				case 2:	##### Оператор
				if (($this->prevstate==3 || $this->prevstate==0) && $word!="-") return "Parse error: unexpected '".chr(2).$word.chr(2)."'.";
				$atoms[$n++]=$word;
				break;
				case 3:	##### Левая скобка :(
				$this->depth++;
				$this->prevstate=$this->state;
				if (!is_array($res=$this->SP())) return $res;
				if ($func) {
					if ($this->allowed_funcs[$func]!=count($res) && count($res)) return "Error: ".$this->allowed_funcs[$func]." parameter[s] expected for '".chr(2).$func.chr(2)."'.";
					if (count($this->res)) $atoms[$n] = call_user_func_array($func,$this->res);
					else $atoms[$n] = call_user_func($func);
					if (is_infinite($atoms[$n++])) return "Error: infinum in expression";
				}
				else $atoms[$n++]=$res[0];
				$func='';
				break;
				case 4:	##### Правая скобка :)
				if (!$this->depth) return "Parse error: unexpected ')'";
				$this->depth--;
				$this->res=$this->pEval($atoms);
				return $this->res;
				break;
				case 5:	##### Имя функции
				if (!function_exists($word)) return "Error: function '".chr(2)."$word".chr(2)."' does not exist.";
				if (!array_key_exists($word,$this->allowed_funcs)) return "Error: function '".chr(2)."$word".chr(2)."' is not permited";
				$func=$word;
				
			}
			$this->prevstate=$this->state;
		}
	}
	function pEval($atoms) {
		if (!count($atoms)) return null;
		$str2eval=implode("",$atoms);
		if (substr_count($str2eval,'/0')!=substr_count($str2eval,'/0.')||substr_count($str2eval,'%0')!=substr_count($str2eval,'%0.')) return "Error: division by zero";
		$atoms2eval=explode(',',$str2eval);
		$res=array();
		foreach($atoms2eval as $current) @eval("array_push(\$res,$current);");
		return $res;
	}
}
}

if (!count($arg)) stp($target_local,$nick.": String is void");
else {
	$calc=new calc_parser(strip(implode(" ",$arg),'a'));
	$send($target_local,$calc->SP());
	print "\n";
	unset($calc);
}
?>