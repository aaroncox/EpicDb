<?php
/**
 * EpicDb_Mongo_Meta_Cost
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Meta_Cost extends MW_Mongo_Document
{
	protected $_requirements = array(
		'_type' => array('Document:EpicDb_Mongo_Record_Resource', 'AsReference', 'Required'),
		'value' => array("Filter:Int", 'Required'),
	);
	
} // END class EpicDb_Mongo_Meta_Cost