<?php
/**
 * EpicDb_Mongo_Record
 *
 * undocumented class
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Record extends EpicDb_Auth_Mongo_Resource_Document implements EpicDb_Interface_Revisionable, EpicDb_Interface_Cardable, EpicDb_Interface_Tooltiped
{
	public $summaryHelper = 'recordSummary';
	public $contextHelper = 'recordContext';
	
	protected static $_collectionName = 'records';
	protected static $_documentType = null;
	protected static $_documentSetClass = 'EpicDb_Mongo_Records';
	protected static $_editForm = 'EpicDb_Form_Record';
	protected static $_revisionProperties = array('name', 'description', 'attribs', '_type', 'tags', '_lastEditedBy'); 

	/**
	 * __construct - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function __construct($data = array(), $config = array())
	{
		$this->addRequirements(array(
			'_lastEditedBy' => array('Document:EpicDb_Mongo_Profile', 'AsReference'),
			'revisions' => array('DocumentSet'),
			'revisions.$' => array('Document:EpicDb_Mongo_Revision'),
			'tags' => array('DocumentSet:EpicDb_Mongo_Tags'),
			'attribs' => array('Document:EpicDb_Mongo_Meta'),
		));
		return parent::__construct($data, $config);
	}

	public function newRevision()
	{
		$revision = $this->revisions->new();
		foreach (static::$_revisionProperties as $key) {
			$revision->$key = $this->$key;
		}
		$this->revisions->addDocument($revision);
	}

	public function getPropertyClass($property, $data) {
		if (isset($data['_type'])) {
			return EpicDb_Mongo::db($data['_type']);
		}
	}

	// Returns the string URL of where to load the icon for this
	public function getIcon() {
		if($this->icon) {
			return $this->icon;
		}
		return "http://s3.r2-db.com/unknown.jpg";
	}
	
	// Returns the string name of this
	public function getName() {
		return $this->name;
	}
	
	// Returns the string description
	public function getDescription() {
		return $this->description;
	}
	
	// Returns an array of strings representing view helpers to execute
	public function getTooltipHelpers() {
		return array('icon', 'name', 'description');
	}

	public function cardProperties($view) {
		return array(
			'is a' => $view->recordTypeLink($this)
		);
	}
	
	public function getParentResource() {
		return new EpicDb_Auth_Resource_Record();
	}
	
	public function getMyFollowers() {
		return $this->getFollowers($this);
	}
	public static function getFollowers($record, $limit = 9999) {
		return $return = EpicDb_Mongo::db('profile')->fetchAll(array("following" => $record->createReference()), array(), $limit);
	}
	
	public function toJSON() {
		$filter = new MW_Filter_Slug();
		$return = array(
			'_id' => (string)$this->_id,
			'_type' => $this->_type,
			'_name' => $this->name,
			'_slug' => $this->name,
			'name' => $filter->filter($this->name),
			'icon' => $this->icon,
			'color' => $this->color,
		);
		return $return;
		// echo "<pre>"; var_dump($return); exit;
	}
	
	public function getEditForm() {
		$className = static::$_editForm;
		return new $className(array('record' => $this));
	}
	
	public function getAdminForms() {
		$forms = array();
		$forms['changeIcon'] = new EpicDb_Form_Record_Icon(array("record" => $this, "title" => "Change Icon", "description" => "Change the Icon that this record uses, image should be an 80x80 jpg/png/gif."));
		$forms['edit'] = new EpicDb_Form_Record(array("record" => $this, "title" => "Edit Record", "description" => "Edit the fields for this record."));
		foreach(EpicDb_Mongo::db('seed')->getSeedsForType($this->_type) as $seed) {
			$forms["seed-".$seed->_id] = new EpicDb_Form_Seed_Tag(array('seed' => $seed, 'record' => $this, 'title' => $seed->title, 'description' => $seed->title));
		}
		return $forms;
	}
	
	public function getRelatedPosts() {
		$sort = array("_created" => -1);
		$query = array(
			'tags' =>
				array('$elemMatch' => array(
					'reason' => 'tag',
					'ref' => $this->createReference(),
					)
				)				
			);
		return $results = EpicDb_Mongo::db('post')->fetchAll($query, $sort);
	}
	
	// TODO - This needs to be rewritten to array map the schema or something....
	public static function getTypesArray() {
		$db = self::getMongoDb();
		$result = $db->command(array('distinct' => 'records', 'key' => '_type'));
		$values = $result['values'];
		return array_combine($values, $values);
	}
} // END class EpicDb_Mongo_Record