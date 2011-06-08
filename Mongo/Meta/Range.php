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
		'min' => array('Filter:Int'),
		'max' => array("Filter:Int", 'Required'),
	);
	
} // END class EpicDb_Mongo_Meta_Range