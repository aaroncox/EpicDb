<?php
/**
 * EpicDb_Mongo_Meta_Damage
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Meta_Damage extends MW_Mongo_Document
{
	protected $_requirements = array(
		'min' => array('Int'),
		'max' => array("Int", 'Required'),
	);
	
	public static function damage($min, $max = false) {
		if($max === false) $max = $min;
		$doc = new static();
		$doc->min = $min;
		$doc->max = $max;
		return $doc;
	}
	
	public function __toString() {
		if($this->min) {
			return $this->min." - ".$this->max;			
		}
		return $this->max."";
	}
} // END class EpicDb_Mongo_Meta_Damage