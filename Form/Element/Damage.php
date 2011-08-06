<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Form_Element_Damage extends Zend_Form_Element_Text
{
	protected $_validators = array("Damage" => array('validator' => 'Damage'));
	protected $_filters = array("Damage" => array('filter' => 'Damage'));
} // END class EpicDb_Form_Element_Range