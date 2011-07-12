<?php
/**
 * EpicDb_Mongo_Meta_Range
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Meta_Range extends MW_Mongo_Document
{
	protected $_requirements = array(
		'min' => array('Int'),
		'max' => array("Int", 'Required'),
	);
	
	public function __toString() {
		if($this->min) {
			return $this->min." - ".$this->max;			
		}
		return $this->max."";
	}
	
} // END class EpicDb_Mongo_Meta_Range