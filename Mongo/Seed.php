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
	
	public function renderTitle($record) {
		return str_replace(array("[[NAME]]", "[[TYPE]]"), array($record->name, $record->_type), $this->title);
	}
	
	public function getSeedsForType($type) {
		$query = array(
			'types' => $type,
		);
		return static::fetchAll($query);
	}	
} // END class EpicDb_Mongo_Post_Question_System extends EpicDb_Mongo_Post_Question
