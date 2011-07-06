<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Mongo_Seed extends EpicDb_Auth_Mongo_Resource_Document
{
	protected static $_collectionName = 'seeds';
	protected static $_documentType = 'seed';
	protected static $_documentSetClass = 'EpicDb_Mongo_Posts';
	protected static $_editForm = 'EpicDb_Form_Seed';
	
	protected $_requirements = array(
		'types' => array('Array'),
	);
	
} // END class EpicDb_Mongo_Post_Question_System extends EpicDb_Mongo_Post_Question
