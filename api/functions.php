<?php
function count_array_dimensions($arr) { 
    if (is_array(reset($arr))) 
        return count_array_dimensions(reset($arr)) + 1;
    return 1;
}
 
function html_escape_value_helper(&$value) {
    if (!is_string($value)) return;
    $value = htmlspecialchars($value);
}

function html_escape_array(&$arr, $inner_call=false, $pointer=false, $check_double_escape=false) {

    $is_array = is_array($arr);
    
    if (($check_double_escape && 
        is_array && count_array_dimensions($arr) >= 3 && $arr[0][0][100000]) || !$is_array) 
        return $pointer ? NULL : $arr;
        
    foreach ($arr as $key => &$value) 
        !is_array($value) ? html_escape_value_helper($value) : html_escape_array($value, true); 
        
    if (!$inner_call) {
        if ($check_double_escape) $arr[0][0][100000] = true; 
        if (!$pointer) return $arr;
    }
    
}

function strip_color_codes($server) {
    $tmp = "";
    $skip = false;
    for($i = 0; $i <= strlen($server); $i++) {
        $c = $server[$i];
        if ($c == "") { $skip = true; continue; }
        if ($skip) { $skip = false; continue; }
        $tmp .= $c;
    }
    return $tmp;
}

function getmode($int) {
	switch($int) {
		case 0: return 'team deathmatch';
		case 1: return 'coop edit';
		case 2: return 'deathmatch';
		case 3: return 'survivor';
		case 4: return 'team survivor';
        case 5: return 'ctf';
		case 6: return 'pistol frenzy';
		case 7: return 'bot team deathmatch';
		case 8: return 'bot deathmatch';
		case 9: return 'last swiss standing';
		case 10: return 'one shot,one kill';
		case 11: return 'team one shot,one kill';
		case 12: return 'bot one shot,one kill';
        case 13: return 'hunt the flag';
        case 14: return 'team keep the flag';
        case 15: return 'keep the flag';     
		default: return 'unknown';
	}
}

function get_info($b) {
	 
    for($i=1;$i<=2;$i++) $b->getint();
   
    $se = array();
    
    $se['protocol'] = $b->getint();
    if ($se['protocol'] < 1128) // not supported
        return;
    $se['mode'] = getmode($b->getint());
    $se['players'] = $b->getint();
    $se['time'] = $b->getint();

	$se['map'] = $b->getstring();
    $se['server'] = strip_color_codes($b->getstring());
    
    $se['slots'] = $b->getint();

    $se['mastermode'] = $b->getint();
    


	if (!$se['server']) $se['server'] = 'no server name';
	if (!$se['map']) $se['map'] = '';
	

	return $se;
}

class buf {
    public $stack = array();
    function getc() { 
        return array_shift($this->stack);
    }
    function getint() {  
        $c = $this->getc();
        if ($c == 0x80) { 
            $n = $this->getc(); 
            $n |= $this->getc() << 8; 
            return $n;
        }
        else if ($c == 0x81) {
            $n = $this->getc();
            $n |= $this->getc() << 8;
            $n |= $this->getc() << 16;
            $n |= $this->getc() << 24;
            return $n;
        }
        return $c;
    }
    function getstring($len=100) {
        $r = ""; $i = 0; 
        while (true) { 
            $c = $this->getint();
            if ($c == 0) return $r;
            $r .= chr($c);
        } 
    }
}

?>
