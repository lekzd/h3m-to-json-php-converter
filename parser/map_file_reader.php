<?

class map_file_reader {

	var $data;
	var $array;
	var $lastpos = 0;
	var $pos = 0;
	var $EOF = 0;
	var $filename = ''; 

	function map_file_reader( $filename ) {

		$this->filename = $filename;

		$this->data = gzdecode( file_get_contents( $filename ) );
		$this->binnary_array = str_split( $this->data );
		$this->array = $this->binnary_array;

		file_put_contents($filename.'.010', $this->data);

		foreach ($this->array as $key => $value) {
			$this->array[$key] = bin2hex( $value );
		}

		$this->EOF = count( $this->array );

	}

	function seek( $cnt ) {

		$this->lastpos = $this->pos;
		$this->pos += $cnt;

		return $this;
	}

	function get_byte() {

		$res = $this->array[ $this->pos ];

		$this->seek(1);

		return $res;
	}

	function get_int($length=4) {

		$begin = $this->pos;
		$this->seek( $length );

		return hexdec( implode( array_reverse( array_slice( $this->array, $begin, $length ) ) ) );

	}

	/*

	function get_int($length=4) {

		$res = 0;

		for ($i=$this->pos; $i<$this->pos+$length; $i++) {

			//print_r( $i.':'.$this->array[$i].' ' );

			if ( $this->array[$i] == '00' || $i == $this->pos+$length-1 ) {

				$begin = $this->pos;
				$end = $i - $this->pos;

				if ( !$end ) $end = 1;

				//print_r( ' '.$begin.':'. $end );

				$res = hexdec( implode( array_slice( $this->array, $begin, $end ) ) );

				$this->seek( $length );

				break;
			}

		}

		return $res;
	}
	*/

	function get_str() {

		$res = '';
		$length = $this->get_int();

		$begin = $this->pos;
		$end = $length;

		$res = implode( array_slice( $this->binnary_array, $begin, $end ) );

		$this->seek( $length );

		return win1251_utf8($res);
	}

	function get_bytes_arr( $length ) {

		$res = '';

		$begin = $this->pos;
		$end = $length;

		$res = array_slice( $this->binnary_array, $begin, $end );

		foreach ($res as $key => $value) {			
			$res[$key] = bin2hex( $value );
		}

		$this->seek( $length );

		return $res;
	}



}
	
?>