<?
	
class objects_selector {

	var $objects = false;
	var $file_reader = false;

	function objects_selector($file_reader_obj){

		//$this->objects = json_decode( file_get_contents( 'data/objects_data.json' ) );
		$this->file_reader = $file_reader_obj;

	}

	function get_object($class) {

		switch($class){
             case 5:
             case 65:
             case 66:
             case 67:
             case 68:
             case 69:
                 //ObjectArtefact artefact;
                 break;

             case 6:
                 //ObjectPandora pandora;
                 break;

             case 8:
                 //ObjectShip;
                return $this->parse_ship();
                 break;

             case 17:
             case 20:
             case 42: //lighthouse
                 //ObjectDwelling dwelling;
                 break;

             case 26:
                 //ObjectEvent localevent;
                 break;

             case 33:
             case 219:
                 //ObjectGarrison garrison;
                 break;

             case 34:
             case 70:
                 //ObjectHero hero;
             	return $this->parse_hero();
                 break;
             case 62:
                 //ObjectHero hero;
                 break;

             case 36:
                 //ObjectGrail grail;
                 break;

             case 53:
             	/*
                 switch(objects[obj.objectID].object_number) {
                 case 7:
                     //ObjectAbandonedMine abandoned; // bit0 - mercury, 1 - ore, 2 - sulfur, 
                     break;                         // bit3 - crystal, 4 - gem, 5 - gold
                 default:
                     //ObjectMine mine;
                     break;
                 }                 
                */
                 break;

             case 54:
             case 71:
             case 72:
             case 73:
             case 74:
             case 75:
             case 162:
             case 163:
             case 164:
                 //ObjectMonster monster;
                 break;

             case 76:
             case 79:
                 //ObjectResource res;
                 break;
                 
             case 81:
                 //ObjectScientist scientist;
                 break;

             case 83:
                 //ObjectProphet prophet;
                 break;

             case 87:
                 //ObjectShipyard shipyard;
                 break;

             case 88:
             case 89:
             case 90:
                 //ObjectShrine shrine;
                 break;

             case 91:
             case 59:
                 //ObjectSign sign;
                 break;

             case 93:
                 //ObjectSpell spell;
                 break;

             case 98:
             case 77:
                 //ObjectTown town;
             	return $this->parse_town();
                 break;

             case 101:
                 //Chest;
                return $this->parse_chest();
                 break;

             case 112:
                //ObjectMill;
                return $this->parse_mill();
                break;

             case 113:
                 //ObjectWitchHut whut;
                 break;

             case 215:
                 //ObjectQuestionGuard qguard;
                 break;

             case 216:
                 //ObjectGeneralRandomDwelling dwelling;
                 break;
             case 217:
                 //ObjectLevelRandomDwelling dwelling;
                 break;
             case 218:
                 //ObjectTownRandomDwelling dwelling;
                 break;
             case 220:
                 //ObjectAbandonedMine abandoned;
                 break;
         };

	}

    function parse_guards($data) {

        $ret = Array();

        for ( $i=0; $i<7; $i++ ) {

            $begin = $i*4;
            $end = 4;
            $items = array_slice( $data, $begin, $end );

            $ret[$i] = Array(
                'monsterId' => ($items[1] == '00')? hexdec( $items[0] ) : hexdec( $items[0].$items[1] ),
                'monsterCount' => ($items[3] == '00')? hexdec( $items[2] ) : hexdec( $items[3].$items[2] ),
            );

            if ( $ret[$i]['monsterId'] == 65535 ) {
                $ret[$i]['monsterId'] = -1;
            }

        }

        return $ret;
    }

    function parse_ship() {

        $ret = Array();

        $ret['type'] = 'ship';

        return $ret;
    }

    function parse_chest() {

        $ret = Array();

        $ret['type'] = 'chest';

        return $ret;
    }

	function parse_mill() {

		$ret = Array();

        $ret['type'] = 'mill';

        return $ret;
	}

	function parse_town() {

		$ret = Array();

		$this->file_reader->seek(4);

		$ret['type'] = 'town';

		$ret['owner'] = $this->file_reader->get_int(1);		
		$ret['name'] = ( $this->file_reader->get_int(1) )? $this->file_reader->get_str() : '';
		$ret['guards'] = ( $this->file_reader->get_int(1) )? $this->parse_guards( $this->file_reader->get_bytes_arr(7 * 4) ) : Array();	
		$ret['guardsFormation'] = $this->file_reader->get_int(1);	
		$ret['isBuildings'] = $this->file_reader->get_int(1);	

		if ( $ret['isBuildings'] ) {
			$ret['hasBuildings'] = $this->file_reader->get_bytes_arr(6);
			$ret['canBuildings'] = $this->file_reader->get_bytes_arr(6);
			$ret['isFort'] = (integer)( $ret['hasBuildings'][0] != '00' );
		} else {
			$ret['isFort'] = $this->file_reader->get_int(1);
		}
		
		$ret['mustSpells'] = $this->file_reader->get_bytes_arr(9);
		$ret['canSpells'] = $this->file_reader->get_bytes_arr(9);
		$ret['eventQuantity'] = $this->file_reader->get_int(4);

		$this->file_reader->seek(4);

		return $ret;
	}

	function parse_secondary_skills($data) {

		$ret = Array();
		$skillsCount = count($data)/2;

		for ( $i=0; $i<$skillsCount; $i++ ) {

			$begin = $i*2;
			$end = 2;
			$items = array_slice( $data, $begin, $end );

			$ret[$i] = Array(
				'skillId' => hexdec( $items[0] ),
				'skillLevel' => hexdec( $items[1] )
			);

		}

		return $ret;
	}

	function parse_hero() {

		$ret = Array();

		$this->file_reader->seek(4);

		$ret['type'] = 'hero';

		$ret['owner'] = $this->file_reader->get_int(1);	
		$ret['personId'] = $this->file_reader->get_int(1);
		$ret['name'] = ( $this->file_reader->get_int(1) )? $this->file_reader->get_str() : '';
		$ret['exp'] = ( $this->file_reader->get_int(1) )? $this->file_reader->get_int(4) : 0;
		$ret['portrait'] = ( $this->file_reader->get_int(1) )? $this->file_reader->get_int(1) : -1;
		$ret['isSecondary'] = $this->file_reader->get_int(1);	

		if ( $ret['isSecondary'] ) {
			$ret['secondaryCount'] = $this->file_reader->get_int(4);
			$data = $this->file_reader->get_bytes_arr( 2 * $ret['secondaryCount'] );
			$ret['secondarySkills'] = $this->parse_secondary_skills( $data );
		}

		$ret['guards'] = ( $this->file_reader->get_int(1) )? $this->parse_guards( $this->file_reader->get_bytes_arr(7*4) ) : Array();	
		$ret['guardsFormation'] = $this->file_reader->get_int(1);

		$ret['isArtefacts'] = $this->file_reader->get_int(1);	
		$ret['artefacts'] = Array();

		if ( $ret['isArtefacts'] ) {

			$arts = Array(
				'head' => $this->file_reader->get_int(2),
				'shoulders' => $this->file_reader->get_int(2),	
				'neck' => $this->file_reader->get_int(2),
				'rightHand' => $this->file_reader->get_int(2),
				'leftHand' => $this->file_reader->get_int(2),
				'trunk' => $this->file_reader->get_int(2),
				'rightRing' => $this->file_reader->get_int(2),
				'leftRing' => $this->file_reader->get_int(2),
				'legs' => $this->file_reader->get_int(2),

				'misk1' => $this->file_reader->get_int(2),
				'misk2' => $this->file_reader->get_int(2),
				'misk3' => $this->file_reader->get_int(2),
				'misk4' => $this->file_reader->get_int(2),

				'machine1' => $this->file_reader->get_int(2),
				'machine2' => $this->file_reader->get_int(2),
				'machine3' => $this->file_reader->get_int(2),
				'machine4' => $this->file_reader->get_int(2),

				'magickBook' => $this->file_reader->get_int(2),

				'misk5' => $this->file_reader->get_int(2),

				'knapsackCount' => $this->file_reader->get_int(2),
				'knapsack' => Array(),
			);

			if ( $arts['knapsackCount'] ) {
				$max = $arts['knapsackCount'];
				for ( $i=0; $i<$max; $i++ ) {
					$arts['knapsack'][] = $this->file_reader->get_int(2);
				}
			}

			$ret['artefacts'] = $arts;

		}

		$ret['aiZoneRadius'] = $this->file_reader->get_int(1);	
		$ret['biography'] = ( $this->file_reader->get_int(1) )? $this->file_reader->get_str() : '';
		$ret['gender'] = $this->file_reader->get_int(1);	

		$ret['spells'] = ($this->file_reader->get_int(1))? $this->file_reader->get_bytes_arr(9) : Array();

		$ret['isPrimary'] = $this->file_reader->get_int(1);
		$ret['primaries'] = Array();

		if ( $ret['isPrimary'] ) {
			$ret['primaries'] = Array(
				'offence' => $this->file_reader->get_int(1),
				'defence' => $this->file_reader->get_int(1),
				'power' => $this->file_reader->get_int(1),
				'knowledge' => $this->file_reader->get_int(1)
			);
		}

		$this->file_reader->seek(16);

		return $ret;
	}

}

?>