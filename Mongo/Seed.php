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
		'tag' => array('Required'),
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
	public function wiki(EpicDb_Mongo_Record $record) {
		return EpicDb_Mongo::db('wiki')->get($record, $this->tag);
	}
	
	public function tagged(EpicDb_Mongo_Record $record) {
		$seed = $this;
		$tag = $seed->tag;
		$tagDb = $seed->tagDb;
		if($tagDb) {
			$query = array(
				'tags' => array(
					'$elemMatch' => array(
						'reason' => $tag,
						'ref' => $record->createReference(),
					)
				)
			);
			$results = EpicDb_Mongo::db($tagDb)->fetchAll($query);
			$tags = array();
			foreach($results as $result) {
				$tags[] = $result;
			}
		} elseif($tag) {
			$tags = $record->tags->getTags($tag); 
		} else {
			$tags = array();
		}
		return $tags;
	}
} // END class EpicDb_Mongo_Post_Question_System extends EpicDb_Mongo_Post_Question
