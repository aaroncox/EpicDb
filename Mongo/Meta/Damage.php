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
		'min' => array('Filter:Int'),
		'max' => array("Filter:Int"),
	);
	
	public static function damage($min, $max = false) {
		if($max === false) $max = $min;
		$doc = new static();
		$doc->min = $min;
		$doc->max = $max;
		return $doc;
	}
} // END class EpicDb_Mongo_Meta_Damage