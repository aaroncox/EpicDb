<?php
/**
 * EpicDb_Mongo_DocumentSet_Dynamic
 *
 * Class designed for dynamicly setting the type of embedded documentsets
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_DocumentSet_Dynamic extends Shanty_Mongo_DocumentSet
{
	public function getPropertyClass($property, $data) {
		// var_dump($property, $data); 
		if (isset($data['_type'])) {
			return EpicDb_Mongo::dbClass($data['_type']);
		}
	}
	
} // END class EpicDb_Mongo_Profile_Following