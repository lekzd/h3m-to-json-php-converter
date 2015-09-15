<?
	
class map_parser {

	const G__MAX_PLAYERS_COUNT = 8;

	var $map_data;
	var $map_file;

	var $has_heroes;
	var $has_caves;
	var $ground_levels = 1;
	var $map_width = 0;
	var $map_height = 0;

	var $version = 'RoE';

	function map_parser( $filename ) {

		$this->map_file = new map_file_reader( $filename.'.h3m' );
		$this->objects_selector = new objects_selector( $this->map_file );
		$this->map_data = new map( $this->map_file );

		$this->parse();

	} 

	function parse() {

		$this
			->get_version()
			->get_has_heroes()
			->get_map_size()
			->get_has_caves()
			->get_title()
			->get_description()
			->get_difficulty()
			->get_max_heroes_level()

			->get_players_data()			
			->get_victory_conditions()
			->get_defeat_conditions()
			->get_teams_count()

			->get_enabled_heroes()
			->get_disposed_heroes_count()

			->skip_31_unknown_bytes() 

			->get_enabled_artefacts()
			->get_enabled_spells()
			->get_enabled_abilities()

			->get_rumors()

			->skip_157_unknown_bytes()

			->get_map_tiles()

			->get_map_objects()

			->get_tuned_objects()
			->get_global_events()
			;

	}

	//////////////////////////////////////////////////////////////

	function get_player($id) {

		$mb_human = hexdec( $this->map_file->get_byte() );
		$mb_computer = hexdec( $this->map_file->get_byte() );

		if ( !$mb_human && !$mb_computer ) {
			$this->map_file->seek(13);

			return false;
		}

		$this->map_data->players[$id]['heroes'] = Array();

		$this->map_data->players[$id]['active'] = true;

		$this->map_data->players[$id]['mb_human'] = $mb_human;
		$this->map_data->players[$id]['mb_computer'] = $mb_computer;
		$this->map_data->players[$id]['ai_type'] = hexdec( $this->map_file->get_byte() );
		$this->map_data->players[$id]['has_town_on_start'] = hexdec( $this->map_file->get_byte() );

		$this->map_data->players[$id]['cities_type'] = hexdec( $this->map_file->get_byte() );
		$this->map_file->seek(1);
		$this->map_data->players[$id]['random_city_type'] = hexdec( $this->map_file->get_byte() );

		$has_main_town = hexdec( $this->map_file->get_byte() );

		if ( $has_main_town ) {
			$this->map_data->players[$id]['has_main_town'] = true;
			$this->map_file->seek(2);
			$this->map_data->players[$id]['main_town_coords'] = $this->map_file->get_bytes_arr(3);
			$this->map_file->seek(1);
		}

		//9

		if ( hexdec( $this->map_file->get_byte() ) > 0 ) {
			$this->map_file->seek(1);
			$this->map_file->get_str();
			$this->map_file->seek(1);
		}

		$heroes_count = $this->map_file->get_int();

		for ($i=0; $i<$heroes_count; $i++) {

			$this->map_data->players[$id]['heroes'][] = Array(

				'id' => hexdec( $this->map_file->get_byte() ),
				'name' => $this->map_file->get_str()

			);

		}		


		//$this->map_file->seek(13);


		//$this->map_data->players[$id]

	}

	function get_version() {

		$this->map_data->prop( 'version',  $this->map_file->get_int() );

		$version_data = $this->map_data->get('version');
		$this->version = $version_data['value'];

		return $this;
	}

	function get_has_heroes() {

		$this->map_data->prop( 'has_heroes',  $this->map_file->get_byte(), 'boolean' );

		return $this;
	}

	function get_map_size() {

		$this->map_data->prop( 'map_size',  $this->map_file->get_int() );

		$map_size_data = $this->map_data->get('map_size');
		$this->map_width = $map_size_data['hex_value'];
		$this->map_height = $map_size_data['hex_value'];


		return $this;
	}

	function get_has_caves() {

		$this->map_data->prop( 'has_caves',  $this->map_file->get_byte(), 'boolean' );

		$has_caves_data = $this->map_data->get('has_caves');

		if ( hexdec( $has_caves_data['hex_value'] ) ) {
			$this->ground_levels = 2;
		}

		return $this;
	}

	function get_title() {

		$this->map_data->prop( 'title',  $this->map_file->get_str() );

		return $this;
	}

	function get_description() {

		$this->map_data->prop( 'description',  $this->map_file->get_str() );

		return $this;
	}

	function get_difficulty() {

		$this->map_data->prop( 'difficulty',  $this->map_file->get_byte() );

		return $this;

	}

	function get_max_heroes_level() {

		$this->map_data->prop( 'max_heroes_level',  $this->map_file->get_byte(), 'limits' );

		return $this;
		
	}

	// TODO: ñäåëàòü ÷òåíèå äàííûõ èãðîêîâ
	function get_players_data() {

		$has_heroes_data = $this->map_data->get('has_heroes');
		$has_heroes = hexdec( $has_heroes_data['hex_value'] );

		if ( !$has_heroes ) {

			$this->map_file->seek(120);

		} else {

			$this->get_player(0);
			$this->get_player(1);
			$this->get_player(2);
			$this->get_player(3);
			$this->get_player(4);
			$this->get_player(5);
			$this->get_player(6);
			$this->get_player(7);

		}

		return $this;

	}

	// TODO: ñäåëàòü ÷òåíèå âñåõ óñëîâèé ïîáåäû
	function get_victory_conditions() {

		$value = $this->map_file->get_byte();
		$data = '';

		switch ($value) {
			case 'ff': {	
				// íåò			
				break;
			}
			case '00': {
				// ïîëó÷èòü îïðåäåëåííûé àðòåôàêò
				$data = $this->map_file->get_bytes_arr(4);
				break;
			}
			case '01': {
				// ñîáðàòü ñóùåñòâà
				$data = $this->map_file->get_bytes_arr(8);
				break;
			}
			case '02': {
				// íàêîïèòü ðåñóðñû
				$data = $this->map_file->get_bytes_arr(7);
				break;
			}
			case '03': {
				// óëó÷øèòü îïðåäåëåííûé ãîðîä
				//$data = $this->map_file->get_bytes_arr(1);
				break;
			}
			case '04': {
				// âîçâåòè ïîñòðîéêó Ãðààëÿ
				//$data = $this->map_file->get_bytes_arr(1);
				break;
			}
			case '05': {
				// ñîêðóøèòü îïðåëåííûé ãîðîä
				//$data = $this->map_file->get_bytes_arr(1);
				break;
			}
			case '06': {
				// ïîáåäèòü îïðåäåëåííîãî ãåðîÿ
				//$data = $this->map_file->get_bytes_arr(1);
				break;
			}
			case '07': {
				// ïîáåäèòü îïðåäåëåííîãî ìîíñòðà
				//$data = $this->map_file->get_bytes_arr(1);
				break;
			}
			case '08': {
				// çàõâàòèòü âñå èñòî÷íèêè ðåñóðñîâ
				$data = $this->map_file->get_bytes_arr(2);
				break;
			}
			case '09': {
				// çàõâàòèòü âñå ëîãîâà ñóùåñòâ
				$data = $this->map_file->get_bytes_arr(2);
				break;
			}
			case '10': {
				// ïåðåíåñòè àðòåôàêò
				//$data = $this->map_file->get_bytes_arr(1);
				break;
			}
		}

		$this->map_data->prop( 'victory_conditions',  $value );

		return $this;

	}

	// TODO: ñäåëàòü ÷òåíèå âñåõ óñëîâèé ïîðàæåíèÿ
	function get_defeat_conditions() {

		$value = $this->map_file->get_byte();
		$data = '';

		switch ($value) {
			case 'ff': {	
				// íåò			
				break;
			}
			case '00': {
				// ïîòåðÿ ãîðîäà
				//$data = $this->map_file->get_bytes_arr(4);
				break;
			}
			case '01': {
				// ïîòåðÿ ãåðîÿ
				//$data = $this->map_file->get_bytes_arr(8);
				break;
			}
			case '02': {
				// ïî èñòå÷åíèè ñðîêà
				$data = $this->map_file->get_bytes_arr(2);
				break;
			}
		}

		$this->map_data->prop( 'defeat_conditions',  $value );

		return $this;

	}

	// TODO: òåñòèòü è äîäåëàòü
	function get_teams_count() {

		$value = (int)$this->map_file->get_byte();
		$data = '';

		if ( $value != 0 ) {
			$data = $this->map_file->get_bytes_arr(7);
		}

		$this->map_data->prop( 'teams_count',  $value );

		return $this;
	}

	// TODO: òåñòèòü è äîäåëàòü
	function get_enabled_heroes() {

		if ( $this->version == 'RoE' ) {
			$data = $this->map_file->get_bytes_arr(16);
		} else {
			$data = $this->map_file->get_bytes_arr(20);
		}		

		$this->map_file->seek(4);

		return $this;
	}

	function get_disposed_heroes_count() {

		$value = (int)$this->map_file->get_byte();

		$this->map_data->prop( 'disposed_heroes_count',  $value );

		return $this;

	}

	// TODO: òåñòèòü è äîäåëàòü
	function skip_31_unknown_bytes() {
		$this->map_file->seek(31);

		return $this;
	}

	// TODO: òåñòèòü è äîäåëàòü
	function get_enabled_artefacts() {
		if ( $this->version == 'WoD' ) {
			$this->map_file->seek(18);
		} else {
			$this->map_file->seek(17);
		}

		return $this;
	}
	
	// TODO: òåñòèòü è äîäåëàòü
	function get_enabled_spells() {
		if ( $this->version == 'WoD' ) {
			$this->map_file->seek(8);
		} else {
			//$this->map_file->seek(0);
		}

		return $this;
	}

	// TODO: òåñòèòü è äîäåëàòü
	function get_enabled_abilities() {
		if ( $this->version == 'WoD' ) {
			$this->map_file->seek(4);
		} else {
			//$this->map_file->seek(0);
		}

		$this->map_file->seek(1);

		return $this;
	}

	function get_rumors() {

		$count = $this->map_file->get_int();

		for ($i=0; $i<$count; $i++) {

			$this->map_data->prop( 'rumor_'.$i.'_title',  $this->map_file->get_str() );
			$this->map_data->prop( 'rumor_'.$i.'_text',  $this->map_file->get_str() );

		}

		return $this;

	}

	// TODO: òåñòèòü è äîäåëàòü
	function skip_157_unknown_bytes() {
		$this->map_file->seek(155);

		return $this;
	}

	function get_map_tiles() {

		for ($z=0; $z<$this->ground_levels; $z++) {

			for ( $y=0; $y < $this->map_height; $y++ ) {

				for ( $x=0; $x < $this->map_width; $x++ ) {

					$new_tile = $this->map_file->get_bytes_arr(7);
					$new_tile[] = (int)$x;
					$new_tile[] = (int)$y;
					$new_tile[] = (int)$z;
					$this->map_data->tiles[] = $new_tile;

				}

			}

		}

		$this->map_file->seek(1);

		return $this;
	}

	function get_map_objects() {

		$objects_count = $this->map_file->get_int();

		for ($i=0;$i<$objects_count;$i++) {

			$object_texture_name = $this->map_file->get_str();

			$this->map_data->objects[] = Array(
 
				'texture' => $object_texture_name,				
				'passability' => $this->map_file->get_bytes_arr(6),
				'actions' => $this->map_file->get_bytes_arr(6),
				'landscape' => $this->map_file->get_int(2),
				'land_edit_groups' => $this->map_file->get_int(2),

				'object_class' => $this->map_file->get_int(4),
				'object_number' => $this->map_file->get_int(4),
				'object_group' => $this->map_file->get_int(1),

				'is_overlay' => $this->map_file->get_int(1),

			);

			$this->map_file->seek(16);
		}

		return $this;
	}

	function get_tuned_objects() {

		$tuned_object_counts = $this->map_file->get_int();

		for ($i=0;$i<$tuned_object_counts;$i++) {

			$coords = $this->map_file->get_bytes_arr(3);
			$obj_template_id = $this->map_file->get_int();
			$obj_class = $this->map_data->objects[ $obj_template_id ]['object_class'];

			$this->map_file->seek(5);

			for ($j=0; $j<3; $j++) {
				$coords[$j] = hexdec($coords[$j]);
			}

			$obj_id = $obj_template_id;

			if ( isset($this->map_data->objects[ $obj_template_id ]['coords']) ) {
				$this->map_data->objects[] = $this->map_data->objects[ $obj_template_id ];
				$obj_id = count( $this->map_data->objects )-1;
			}

			$this->map_data->objects[ $obj_id ]['coords'] = $coords;

			if ( $obj_class ) {				
				$this->map_data->objects[ $obj_id ]['data'] = $this->objects_selector->get_object( $obj_class );
			}
			
		}

		//*
			
		echo '<pre>';
		print_r( $this->map_data->objects );
		echo '</pre>';

		//*/

		return $this;
	}

	function get_global_events() {

		$count = $this->map_file->get_int(4);

		return $this;
	}


	//////////////////////////////////////////////////////////////

	function __destruct() {	

		print_r( '<br/>' );
		echo $this->map_data->get_html_table();
		//print_r( $this->map_data->props );

		$console = new hex_console( $this->map_file );
		echo $console->get();

		$this->map_data->write_json();
	}

}
?>