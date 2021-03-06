<?php
/**
 * EpicDb_Mongo_Post
 *
 * Post Mongo Object
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 **/
class EpicDb_Mongo_Post extends EpicDb_Auth_Mongo_Resource_Document implements EpicDb_Interface_Revisionable, EpicDb_Interface_Tooltiped, EpicDb_Vote_Interface_Flaggable, EpicDb_Interface_TagMeta
{
	public $contextHelper = 'context';
	public $routeName = "post";
	
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
			'_touchedBy' => array('Document:EpicDb_Mongo_Profile', 'AsReference'),
			'_lastEditedBy' => array('Document:EpicDb_Mongo_Profile', 'AsReference'),
			'_deletedBy' => array('Document:EpicDb_Mongo_Profile_User', 'AsReference'),
			// '_profile' => array('Document:EpicDb_Mongo_Profile', 'AsReference', 'Required'),
			'tags' => array('DocumentSet:EpicDb_Mongo_Tags'),
			'touchedBy' => array('Document:EpicDb_Mongo_Profile', 'AsReference'),
			'revisions' => array('DocumentSet'),
			'revisions.$' => array('Document:EpicDb_Mongo_Revision'),
			'revisions.$.tags' => array('DocumentSet:EpicDb_Mongo_Tags'),
		));
		return parent::__construct($data, $config);
	}
	
	// Returns the string URL of where to load the icon for this
	public function getIcon() {
		if($poster = $this->tags->getTag('source')?:$this->tags->getTag('author')) {
			if ( method_exists( $poster, 'getIcon' ) ) {
				return $poster->getIcon();
			}
		}
		return "";
	}
	
	// Returns the string name of this
	public function getName() {
		if($this->title) return $this->title;
		return "";
	}
	
	// Returns the string description
	public function getDescription() {
		if($this->body) return $this->body;
		return "";
	}
	
	// Returns an array of strings representing view helpers to execute
	public function getTooltipHelpers() {
		return array("title", "body", array('cloud', $this->tags, 'Tags'));
	}

	public function getPermaLink( $view )
	{
		return $view->url($this->getRouteParams(), $this->_routeName, true);
	}
	
	public function getParentResource() {
		return new EpicDb_Auth_Resource_Post(!$this->_private);
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
		return parent::getPropertyClass( $property, $data );
	}

	public function destroy() {
		return parent::delete();
	}

	public static function fetchOne($query = array()) {
		$roles = array();
		foreach(EpicDb_Auth::getInstance()->getUserRoles() as $role) {
			$roles[] = $role->createReference();
		}
		$query['_viewers'] = array('$in' => $roles);
		return parent::fetchOne($query);
	}
	
	public static function fetchAll($query = array(), $sort = array(), $limit = false, $skip = false) {
		$roles = array();
		foreach(EpicDb_Auth::getInstance()->getUserRoles() as $role) {
			$roles[] = $role->createReference();
		}
		if(!isset($query['_published'])) {
			$query['_published'] = array('$ne' => false);			
		}
		$query['_viewers'] = array('$in' => $roles);
		return parent::fetchAll($query, $sort, $limit, $skip);
	}

	public function findResponses( $query = array(), $sort = array(), $limit = false ) {
		$query = array(
			"_parent" => $this->createReference(),
		) + $query;
		$sort += array("_created" => 1);
		return $results = EpicDb_Mongo::db('post')->fetchAll($query, $sort, $limit);
	}

	public function findComments($query = array(), $sort = array(), $limit = 1000) {
		return $this->findResponses( array( "_type" => "comment" ) + $query, $sort, $limit );
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
	
	public static function getPopularTags($limit = 99) {
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
			$tags[$record->slug] = $record;
		}
		return $tags;		
	}

	public function getEditForm($params = array()) {
		$className = static::$_editForm;
		return new $className(array('post' => $this)+$params);
	}

	public function newRevision()
	{
		$revision = $this->revisions->new();
		$this->revisions->addDocument($revision);
		$copy = array('source', 'body', '_lastEditedBy', '_lastEditedReason', '_lastEdited', 'title', 'tldr');
		foreach ($copy as $key) {
			$revision->$key = $this->$key;
		}
		// clone the tags to make sure updates past this point aren't saved in the revision
		$revision->tags = clone $this->tags;
	}
	
	public function bump($by = null) {
		$this->touched = time();
		if($by) {
			$this->touchedBy = $by;
		}
		$this->save();
	}
	
	public function save() {
		if (!$this->touched && ($author = $this->tags->getTag('author')) && $author instanceOf EpicDb_Mongo_Profile) {
			$this->touched = $this->_created;
			$this->touchedBy = $this->tags->getTag('author');
		}
		
		// This could probably be handled elsewhere better? Just pushing things forward

		// This is how this should be handled...
		// $this->_viewers->setFromArary($this->getRolesWithPrivilege('view'));

		// This is how it works right now...
		$viewers = $this->getRolesWithPrivilege('view');
		for($i = 0; $i < count($viewers); $i++) {
			$this->_viewers->setProperty($i, $viewers[$i]);
		}
		for(;$i<count($this->_viewers); $i++) {
			$this->_viewers->setProperty($i, null);
		}

		if ( !$this->votes ) {
			$this->votes = array( "score" => 0 );
		}

		return parent::save();
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
	
	public function getPublicPosts() {
		$query['_deleted'] = array('$exists' => false);
		$query['_created'] = array('$ne' => false);
		$sort = array("_created" => -1);
		// Make sure I have the permissions to view this post
		foreach(EpicDb_Auth::getInstance()->getUserRoles() as $role) {
			$roles[] = $role->createReference();
		}
		$query['_viewers'] = array('$in' => $roles);

		$results = EpicDb_Mongo::db('post')->fetchAll($query, $sort);
		return $results;
	}
	
	public function findRelated($record, $query = array(), $sort = array(), $limit = false) {
		$metaArray = array();
		if($record instanceOf EpicDb_Interface_TagMeta) {
			foreach($record->getTagMeta() as $key => $value) {
				$metaArray['tags.'.$key] = $value;
			}			
		}
		if(is_array($record)) {
			foreach($record as $document) {
				$query['$or'][] = array(
					'tags.ref' => $document->createReference(),
					'tags.reason' => 'tag'
				)+$metaArray;
				$query['$or'][] = array(
					'tags.ref' => $document->createReference(),
					'tags.reason' => 'subject'
				)+$metaArray;
			}
		} else {
			$query['$or'][] = array(
				'tags.ref' => $record->createReference(),
				'tags.reason' => 'tag'
			)+$metaArray;
			$query['$or'][] = array(
				'tags.ref' => $record->createReference(),
				'tags.reason' => 'subject'
			)+$metaArray;			
		}
		if(empty($sort)) {
			$sort = array("_created" => -1);			
		}
		return EpicDb_Mongo::db('post')->fetchAll($query, $sort, $limit);
	}
		
	public function getVotes($type = false) {
		$query = array(
			'post' => $this->createReference()
		);
		if($type) $query['vote'] = $type;
		return EpicDb_Mongo::db('vote')->fetchAll($query)->makeDocumentSet();
	}
	
	public function delete() {
		$this->_deleted = time();
		$this->_deletedBy = EpicDb_Auth::getInstance()->getUserProfile();
		$this->save();
	}

	public function undelete() {
		unset($this->_deleted);
		unset($this->_deletedBy);
		$this->save();
	}
	
	public function getRouteParams() {
		$filter = new MW_Filter_Slug();
		return array('post' => $this, 'slug' => $filter->filter($this->title));
	}
	
	public function getCachePrefix() {
		return str_replace("-","_",$this->_type."_".$this->id);
	}
	
	public function isReputationDisabled() {
		return $this->disableRep || ( $this->_parent && $this->_parent->id && $this->_parent->isReputationDisabled() );
	}
	
	public function getTagMeta() {
		return array();
	}
	
	public function setTagMeta($tag) {
		return $this;
	}
	
	public function getSearchCache($data = array()) {
		return array(
			'name' => $this->title,
			'description' => $this->source,
			'tags' => $this->tags,
		)+$data;
	}
	
	public function postSave() {
		// Generate the SearchResult cache
		$keywords = array($this->title, $this->source); 
		foreach($this->tags as $tag) {
			if($tag->name) {
				$keywords[] = $tag->name;				
			}
		}
		$filter = new MW_Filter_Slug();
		$url = "/".$this->_type."/".$this->id."/".$filter->filter($this->title);
		$icon = null;
		if($poster = $this->tags->getTag('author')?:$this->tags->getTag('source')) {
			if($poster instanceOf EpicDb_Mongo_Profile) {
				$icon = $poster->getIcon();							
			}
		}
		$score = 0;
		if($this->votes && isset($this->votes['score'])) {
			$score = $this->votes['score'];
		} 
		if(!$title = $this->title) {
			$title = substr(strip_tags(trim($this->body)), 0, 60);
		}
		EpicDb_Mongo::db('search')->generate(array(
			'records' => array($this),
			'keywords' => $keywords,
			'name' => $title,
			'type' => $this->_type,
			'tags' => $this->tags,
			'icon' => $icon,
			'score' => $score,
			'url' => $url,
		));
		return parent::postSave();
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