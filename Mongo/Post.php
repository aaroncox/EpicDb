<?php
/**
 * EpicDb_Mongo_Post
 *
 * Post Mongo Object
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 **/
class EpicDb_Mongo_Post extends MW_Auth_Mongo_Resource_Document implements EpicDb_Interface_Revisionable
{
	public $contextHelper = 'context';
	
	protected static $_collectionName = 'posts';
	protected static $_documentType = null;
	protected static $_documentSetClass = 'EpicDb_Mongo_Posts';
	protected static $_editForm = 'EpicDb_Form_Post';
	protected $_routeName = 'post';
	/**
	 * __construct - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function __construct($data = array(), $config = array())
	{
		$this->addRequirements(array(
			'_viewers' => array("DocumentSet"),
			'_viewers.$' => array('Document:MW_Auth_Mongo_Role', 'AsReference'),
			// '_record' => array('Document:R2Db_Mongo', 'AsReference'),
			'_parent' => array('Document:EpicDb_Mongo_Post', 'AsReference'),
			'_lastEditedBy' => array('Document:EpicDb_Mongo_Profile', 'AsReference'),
			// '_profile' => array('Document:EpicDb_Mongo_Profile', 'AsReference', 'Required'),
			'tags' => array('DocumentSet:EpicDb_Mongo_Tags', 'Required'),
			'touchedBy' => array('Document:EpicDb_Mongo_Profile_User', 'AsReference'),
			'revisions' => array('DocumentSet'),
			'revisions.$' => array('Document:EpicDb_Mongo_Revision'),
		));
		return parent::__construct($data, $config);
	}

	public function getPermaLink( $view )
	{
		return $view->url(array(
			'post' => $this
		), $this->_routeName, true);
	}
	
	public function getParentResource() {
		return new EpicDb_Auth_Resource_Post();
	}
	
	public static function getDocumentClass($data = array()) {
		if (isset($data['_type'])) {
			return EpicDb_Mongo::db($data['_type']);
		} else { 
			return EpicDb_Mongo::dbClass('post');
		}
	}

	public function getPropertyClass($property, $data) {
		if (isset($data['_type'])) {
			return EpicDb_Mongo::dbClass($data['_type']);
		}
	}

	public function findResponses($limit = 10, $query = array(), $sort = array()) {
		$query = array(
			"_parent" => $this->createReference(),
			'_deleted' => array(
					'$exists' => false
				)
		);
		$sort = array("_created" => 1);
		return $results = EpicDb_Mongo::db('post')->fetchAll($query, $sort, $limit);
	}

	public static function getTagsByUsage($limit = 99) {
		// TODO - XHProf Improvement: This function eats up about 1 second of processing time, needs caching or something.
		// TODO - Actually it crashes as soon as it finds a user tagged as a tag, with 'unknown collection profiles'.
		$query = array();
		$map = new MongoCode("function() {
			this.tags.forEach(function(ref) {
				if(ref.reason == 'tag') {
					emit(ref, 1);
				}
			})
		}");
		$reduce = new MongoCode("function(key, values) {
			var sum = 0;
			for (var i in values) { sum += values[i]; }
			return sum;
		}");

		$db = self::getMongoDb();
		$result = $db->command(array(
				"mapreduce" => static::$_collectionName,
				"map" => $map,
				"reduce" => $reduce,
				"query" => $query,
		));
		$tags = array();
		$data = $db->selectCollection($result['result']);
		foreach($data->find()->sort(array('value' => -1, 'name' => 1))->limit($limit) as $d) {
			$record = EpicDb_Mongo::db($d['_id']['ref']['$ref'])->find($d['_id']['ref']['$id']);
			$tags[$record->slug] = array(
				'record' => $record,
				'count' => $d['value'],
			);
		}
		return $tags;
	}

	public function getEditForm() {
		$className = static::$_editForm;
		return new $className(array('post' => $this));
	}

	public function newRevision()
	{
		$revision = $this->revisions->new();
		$this->revisions->addDocument($revision);
		$copy = array('source', 'body', '_lastEditedBy', '_lastEditedReason', '_lastEdited', 'title', 'tldr','tags');
		foreach ($copy as $key) {
			$revision->$key = $this->$key;
		}
	}
	
	public function bump($by = null) {
		$this->touched = time();
		if($by) {
			$this->touchedBy = $by;
		}
		$this->save();
	}
	
	public function save() {
		if (!$this->touched && $this->tags->getTag('author')) {
			$this->touched = $this->_created;
			$this->touchedBy = $this->tags->getTag('author');
		}
		// This could probably be handled elsewhere better? Just pushing things forward
		if($this->_viewers->export() == array()) {
			// Else lets give everyone access.
			$this->_viewers->addDocument(MW_Auth_Mongo_Role::getGroup(MW_Auth_Group_Guest::getInstance()));
			$this->_viewers->addDocument(MW_Auth_Mongo_Role::getGroup(MW_Auth_Group_User::getInstance()));
		}
		return parent::save();
	}
	
	public function findComments($limit = 10, $query = array(), $sort = array()) {
		// var_dump($this->createReference());
		$query = array(
			"_parent" => $this->createReference(),
			'_deleted' => array(
					'$exists' => false
				)
		)+$query;
		$sort = array("_created" => 1);
		return $results = EpicDb_Mongo::db('comment')->fetchAll($query, $sort, $limit);
	}

	public function getRelatedPosts() {
		$tags = $this->tags->getTags('tag'); 
		if(empty($tags)) return array();
		// var_dump($tags); exit;
		$query = array();
		$sort = array("_created" => -1);
		foreach($tags as $tag) {
			$query['$or'][] = array(
				'tags' =>
					array('$elemMatch' => array(
						'reason' => 'tag',
						'ref' => $tag->createReference(),
						)
					)				
				);
		}
		return $results = EpicDb_Mongo::db('post')->fetchAll($query, $sort, 10);
	}
	// This is for watching queries as they execute on posts, perhaps we could enable it by a flag? or mode? I just used it for debugging queries.
	// public static function fetchAll($query = array(), $sort = array(), $limit = false, $skip = false) {
	// 	$writer = new Zend_Log_Writer_Firebug();
	// 	$logger = new Zend_Log($writer);
	// 	$output = array(
	// 		Zend_Json::encode($query),
	// 		Zend_Json::encode($sort),
	// 	);
	// 	$logger->log($output, Zend_Log::INFO);
	// 	return parent::fetchAll($query, $sort, $limit, $skip);
	// }
} // END class EpicDb_Mongo_Post