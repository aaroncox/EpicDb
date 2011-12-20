<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Mongo_Seed extends EpicDb_Auth_Mongo_Resource_Document implements EpicDb_Interface_Tooltiped
{
	protected static $_collectionName = 'seeds';
	protected static $_documentType = 'seed';
	protected static $_documentSetClass = 'EpicDb_Mongo_Posts';
	protected static $_editForm = 'EpicDb_Form_Seed';
	public $routeName = 'record';
	
	public $target = null;
	
	protected $_requirements = array(
		'types' => array('Array'),
		'tag' => array('Required'),
	);
	
	// Returns the string URL of where to load the icon for this
	public function getIcon() {
		if($this->target) {
			return $this->target->getIcon();
		}
		return '';
	}
	
	// Returns the string name of this
	public function getName() {
		if($this->target) {
			return $this->renderTitle($this->target);
		}
		return 'Undefined Target';
	}
	
	// Returns the string description
	public function getDescription() {
		$description = "";
		foreach($this->tagged($this->target) as $tag) {
			$description .= $tag->name;
		}
		return $description;
	}
	
	// Returns an array of strings representing view helpers to execute
	public function getTooltipHelpers() {
		return array('icon', 'name', array('cloud', $this->tagged($this->target), 'Answers'));
	}
	
	
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
	
	public function getRouteParams() {
		return array('record' => $this);
	}
	
	public function setTarget($record) {
		$this->target = $record;
	}
} // END class EpicDb_Mongo_Post_Question_System extends EpicDb_Mongo_Post_Question
