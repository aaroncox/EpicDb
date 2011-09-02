<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Form_Element_UseCost extends Zend_Form_Element_Text
{
	protected $_validators = array("UseCost" => array('validator' => 'UseCost'));
	protected $_filters = array("UseCost" => array('filter' => 'UseCost'));
} // END class EpicDb_Form_Element_Range