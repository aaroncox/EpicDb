<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Mongo_Post_Poll_Option extends MW_Mongo_Document
{
	protected $_requirements = array(
		'tags' => array('DocumentSet:EpicDb_Mongo_Tags', 'Required'),
		'voters' => array('DocumentSet:EpicDb_Mongo_Tags', 'Required'),		
	);	
} // END class EpicDb_Mongo_Post_Poll_Option extends MW_Mongo_Document