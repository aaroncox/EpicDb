<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Filter_Damage implements Zend_Filter_Interface
{
	public function filter($value) {
		if($value instanceOf EpicDb_Mongo_Meta_Damage) return $value;
		if($value instanceOf EpicDb_Mongo_Meta_Damage) return $value;
		// Matches: '1-10' '1 - 10' '1m - 10m'
		preg_match('/^\s*(\d+(?:\.\d+)?)[-m\s]*(\d+(?:\.\d+)?)?/', $value, $result);
		if($result) {
			$return = new EpicDb_Mongo_Meta_Damage();
			if(!empty($result[2])) {				
				$return->min = $result[1];
				$return->max = $result[2];
			} else {
				$return->max = $result[1];				
			}
			return $return;
		} else {
			return $value;	
		}
	}
} // END class EpicDb_Filter_Damage extends Zend_Filter_Abstract