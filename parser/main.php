<?

require("map_file_reader.php");
require("map_parser.php");
require("map_data.php");
require("objects_lib.php");

global $_utf8win1251;
global $_win1251utf8;

$_utf8win1251 = array(
"\xD0\x90"=>"\xC0","\xD0\x91"=>"\xC1","\xD0\x92"=>"\xC2","\xD0\x93"=>"\xC3","\xD0\x94"=>"\xC4",
"\xD0\x95"=>"\xC5","\xD0\x81"=>"\xA8","\xD0\x96"=>"\xC6","\xD0\x97"=>"\xC7","\xD0\x98"=>"\xC8",
"\xD0\x99"=>"\xC9","\xD0\x9A"=>"\xCA","\xD0\x9B"=>"\xCB","\xD0\x9C"=>"\xCC","\xD0\x9D"=>"\xCD",
"\xD0\x9E"=>"\xCE","\xD0\x9F"=>"\xCF","\xD0\xA0"=>"\xD0","\xD0\xA1"=>"\xD1","\xD0\xA2"=>"\xD2",
"\xD0\xA3"=>"\xD3","\xD0\xA4"=>"\xD4","\xD0\xA5"=>"\xD5","\xD0\xA6"=>"\xD6","\xD0\xA7"=>"\xD7",
"\xD0\xA8"=>"\xD8","\xD0\xA9"=>"\xD9","\xD0\xAA"=>"\xDA","\xD0\xAB"=>"\xDB","\xD0\xAC"=>"\xDC",
"\xD0\xAD"=>"\xDD","\xD0\xAE"=>"\xDE","\xD0\xAF"=>"\xDF","\xD0\x87"=>"\xAF","\xD0\x86"=>"\xB2",
"\xD0\x84"=>"\xAA","\xD0\x8E"=>"\xA1","\xD0\xB0"=>"\xE0","\xD0\xB1"=>"\xE1","\xD0\xB2"=>"\xE2",
"\xD0\xB3"=>"\xE3","\xD0\xB4"=>"\xE4","\xD0\xB5"=>"\xE5","\xD1\x91"=>"\xB8","\xD0\xB6"=>"\xE6",
"\xD0\xB7"=>"\xE7","\xD0\xB8"=>"\xE8","\xD0\xB9"=>"\xE9","\xD0\xBA"=>"\xEA","\xD0\xBB"=>"\xEB",
"\xD0\xBC"=>"\xEC","\xD0\xBD"=>"\xED","\xD0\xBE"=>"\xEE","\xD0\xBF"=>"\xEF","\xD1\x80"=>"\xF0",
"\xD1\x81"=>"\xF1","\xD1\x82"=>"\xF2","\xD1\x83"=>"\xF3","\xD1\x84"=>"\xF4","\xD1\x85"=>"\xF5",
"\xD1\x86"=>"\xF6","\xD1\x87"=>"\xF7","\xD1\x88"=>"\xF8","\xD1\x89"=>"\xF9","\xD1\x8A"=>"\xFA",
"\xD1\x8B"=>"\xFB","\xD1\x8C"=>"\xFC","\xD1\x8D"=>"\xFD","\xD1\x8E"=>"\xFE","\xD1\x8F"=>"\xFF",
"\xD1\x96"=>"\xB3","\xD1\x97"=>"\xBF","\xD1\x94"=>"\xBA","\xD1\x9E"=>"\xA2");
$_win1251utf8 = array(
"\xC0"=>"\xD0\x90","\xC1"=>"\xD0\x91","\xC2"=>"\xD0\x92","\xC3"=>"\xD0\x93","\xC4"=>"\xD0\x94",
"\xC5"=>"\xD0\x95","\xA8"=>"\xD0\x81","\xC6"=>"\xD0\x96","\xC7"=>"\xD0\x97","\xC8"=>"\xD0\x98",
"\xC9"=>"\xD0\x99","\xCA"=>"\xD0\x9A","\xCB"=>"\xD0\x9B","\xCC"=>"\xD0\x9C","\xCD"=>"\xD0\x9D",
"\xCE"=>"\xD0\x9E","\xCF"=>"\xD0\x9F","\xD0"=>"\xD0\xA0","\xD1"=>"\xD0\xA1","\xD2"=>"\xD0\xA2",
"\xD3"=>"\xD0\xA3","\xD4"=>"\xD0\xA4","\xD5"=>"\xD0\xA5","\xD6"=>"\xD0\xA6","\xD7"=>"\xD0\xA7",
"\xD8"=>"\xD0\xA8","\xD9"=>"\xD0\xA9","\xDA"=>"\xD0\xAA","\xDB"=>"\xD0\xAB","\xDC"=>"\xD0\xAC",
"\xDD"=>"\xD0\xAD","\xDE"=>"\xD0\xAE","\xDF"=>"\xD0\xAF","\xAF"=>"\xD0\x87","\xB2"=>"\xD0\x86",
"\xAA"=>"\xD0\x84","\xA1"=>"\xD0\x8E","\xE0"=>"\xD0\xB0","\xE1"=>"\xD0\xB1","\xE2"=>"\xD0\xB2",
"\xE3"=>"\xD0\xB3","\xE4"=>"\xD0\xB4","\xE5"=>"\xD0\xB5","\xB8"=>"\xD1\x91","\xE6"=>"\xD0\xB6",
"\xE7"=>"\xD0\xB7","\xE8"=>"\xD0\xB8","\xE9"=>"\xD0\xB9","\xEA"=>"\xD0\xBA","\xEB"=>"\xD0\xBB",
"\xEC"=>"\xD0\xBC","\xED"=>"\xD0\xBD","\xEE"=>"\xD0\xBE","\xEF"=>"\xD0\xBF","\xF0"=>"\xD1\x80",
"\xF1"=>"\xD1\x81","\xF2"=>"\xD1\x82","\xF3"=>"\xD1\x83","\xF4"=>"\xD1\x84","\xF5"=>"\xD1\x85",
"\xF6"=>"\xD1\x86","\xF7"=>"\xD1\x87","\xF8"=>"\xD1\x88","\xF9"=>"\xD1\x89","\xFA"=>"\xD1\x8A",
"\xFB"=>"\xD1\x8B","\xFC"=>"\xD1\x8C","\xFD"=>"\xD1\x8D","\xFE"=>"\xD1\x8E","\xFF"=>"\xD1\x8F",
"\xB3"=>"\xD1\x96","\xBF"=>"\xD1\x97","\xBA"=>"\xD1\x94","\xA2"=>"\xD1\x9E");

function utf8_win1251($a) {
    global $_utf8win1251;
    if (is_array($a)){
        foreach ($a as $k => $v) {
            if (is_array($v)) {
                $a[$k] = utf8_win1251($v);
            } else {
                $a[$k] = strtr($v, $_utf8win1251);
            }
        }
        return $a;
    } else {
        return strtr($a, $_utf8win1251);
    }
}

function win1251_utf8($a) {
    global $_win1251utf8;
    if (is_array($a)){
        foreach ($a as $k=>$v) {
            if (is_array($v)) {
                $a[$k] = utf8_win1251($v);
            } else {
                $a[$k] = strtr($v, $_win1251utf8);
            }
        }
        return $a;
    } else {
        return strtr($a, $_win1251utf8);
    }
}
	
class Tile {

	function tile( $hex_array ) {

		$this->apearance 		= $hex_array[0];

		$this->terrain_id 		= $hex_array[1];
		$this->texture_id 		= $hex_array[2];

		$this->river_id 		= $hex_array[3];
		$this->river_texture_id = $hex_array[4];

		$this->road_id 			= $hex_array[5];
		$this->road_texture_id 	= $hex_array[6];

	}

}

class hex_console {

	var $output = '<hr>';

	function hex_console( $map_file ) {

		$arr = $map_file->binnary_array;
		$i = 1;

		foreach ($arr as $key => $value) {

			$hex_char = bin2hex( $value );

			if ( $hex_char == '00' ) {
				$this->output .= "<span class='hex' style='color: #777' title='$key | ".hexdec($hex_char)."'>$hex_char </span>";
			} else {
				$this->output .= "<span class='hex' title='$key | ".hexdec($hex_char)."'>$hex_char </span>";		
			}		

			if ($i == 16 ) {
				$i = 0;
				$this->output .= '<br/>';
			}

			$i++;

		}

	}

	function get() {
		return $this->output;
	}

}

function hex2str($hex) {
	$str = '';
	for($i=0;$i < strlen($hex);$i+=2) {
	    $str.=chr(hexdec(substr($hex,$i,2)));
	}
	return $str;
}

function gzdecode($data) { 
	return gzinflate(substr($data,10,-8)); 
} 

function hex2ansi( $data ) {

	$ret = '';

	for ($i=0; $i<strlen($data); $i=$i+2) {
		$ret .= chr( hexdec( $data[$i] . $data[$i+1] ) );
	}

	return iconv('WINDOWS-1251', 'UTF-8', $ret);

}

?>