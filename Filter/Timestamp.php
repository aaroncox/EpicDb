<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Filter_Timestamp implements Zend_Filter_Interface
{
	public $format = 'Y-M-d H:m:s';
	
	public function filter($value) {
		if(is_int($value)) {
			return date($this->format, $value);
		}
		$value = $value."";
		if($parsed = strtotime($value)) {
			return date($this->format, $parsed);
		}
		return $value;	
	}
} // END class EpicDb_Filter_Damage extends Zend_Filter_Abstract