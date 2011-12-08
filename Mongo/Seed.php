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
	public $routeName = 'record';
	
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
	
	public function getIcon() {
		return "http://s3.r2-db.com/unknown.jpg";
	}
	
	public function getRouteParams() {
		return array('record' => $this);
	}
} // END class EpicDb_Mongo_Post_Question_System extends EpicDb_Mongo_Post_Question
