<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Form_Element_Range extends Zend_Form_Element_Text
{
	protected $_validators = array("Range" => array('validator' => 'Range'));
	protected $_filters = array("Range" => array('filter' => 'Range'));
} // END class EpicDb_Form_Element_Range