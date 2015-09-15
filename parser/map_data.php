<?

class map {

	var $props = Array();
	var $reader;
	var $tiles = Array();

	/////////////////////////////////////////////////
	// Players

	var $players = Array(
		Array(
			'color'=>'red',
			'active'=>false
		),
		Array(
			'color'=>'blue',
			'active'=>false
		),
		Array(
			'color'=>'tan',
			'active'=>false
		),
		Array(
			'color'=>'green',
			'active'=>false
		),
		Array(
			'color'=>'orange',
			'active'=>false
		),
		Array(
			'color'=>'purple',
			'active'=>false
		),
		Array(
			'color'=>'teal',
			'active'=>false
		),
		Array(
			'color'=>'pink',
			'active'=>false
		),
	);

	/////////////////////////////////////////////////
	// Objects

	var $objects = Array();

	/////////////////////////////////////////////////
	// Filters

	var $filter_boolean = Array(
		'00' => 0,
		'01' => 1
	);

	var $filter_version = Array(
		'14' => 'RoE',
		'21' => 'AB',
		'28' => 'WoD'
	);

	var $filter_difficulty = Array(
		'00' => 'easy',
		'01' => 'normal',
		'02' => 'hard',
		'03' => 'enourmous',
		'04' => 'imposible'
	);

	function filter_limits($src) {

		$output = (int)$src;

		if ( !$output ) {
			$output = '-';
		}

		return $output;
	} 

	function filter_map_size($src) {

		$output = (int)$src;

		return $output;
	}

	/////////////////////////////////////////////////


	function map( $map_file_reader ) {
		$this->reader = $map_file_reader;
	}

	function prop( $name, $value, $value_filter='' ) {

		if (!$value_filter) {
			$value_filter = 'filter_'.$name;
		} else {
			$value_filter = 'filter_'.$value_filter;
		}


		if ( isset( $this->$value_filter ) ) {

			$filter = $this->$value_filter;

			if ( is_array( $filter ) ) {

				$this->props[ $name ] = Array(
					'value' => $filter[ $value ],
					'hex_value' => $value,
					'filter'=> $value_filter,
					'hex_start' => $this->reader->lastpos,
					'hex_end' => $this->reader->pos,
				);
			}

		} else {

			if ( method_exists( $this, $value_filter ) ) {

				$this->props[ $name ] = Array(
					'value' => $this->$value_filter( $value ),
					'hex_value' => $value,
					'filter'=> $value_filter,
					'hex_start' => $this->reader->lastpos,
					'hex_end' => $this->reader->pos,
				);
			} else {

				$this->props[ $name ] = Array(
					'value' => $value,
					'hex_value' => $value,
					'filter'=> '',
					'hex_start' => $this->reader->lastpos,
					'hex_end' => $this->reader->pos,
				);
			}

			
		}		

	}

	function get_html_table() {

		$output = '<table border="1">';

		foreach ($this->props as $key => $value) {
			
			$output .= '<tr> <td title="'.$value['hex_start'].':'.$value['hex_end'].'"> '.$key.' </td> <td title="'.$value['hex_value'].'"> '.$value['value'].' </td> </tr>';

		}

		$output .= '</table>';

		$output .= '<style> .hex:nth-of-type(n+'. ($this->reader->pos+1) .') { background: #DBFFCA } </style>';

		return $output;

	}

	function get( $name ) {
		return $this->props[ $name ];
	}

	function write_json() {

		$filename = $this->reader->filename.'.json';
		$data = Array(
			'props' => $this->props,
			'tiles' => $this->tiles,
			'objects' => $this->objects,
			'players' => $this->players
		);
		$json = json_encode( $data );



		file_put_contents( $filename, $json );

		// file_put_contents( $filename, 'app.load_map('.$json.')' );

		//print_r( $this->objects );

		//print_r( $data );

	}


}

?>