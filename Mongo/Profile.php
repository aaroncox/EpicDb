<?php
/**
 * EpicDb_Mongo_Profile
 *
 * undocumented class
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Profile extends EpicDb_Auth_Mongo_Resource_Document implements EpicDb_Interface_Cardable, EpicDb_Interface_Tooltiped, EpicDb_Interface_Routable
{
	public $summaryHelper = 'profileSummary';
	public $contextHelper = 'profileContext';
	public $routeName = "profile";
	
	protected $_layout = "2-column";
	protected static $_defaultAction = 'view'; // You can override this to cause the default action on specific profiles to change.
	protected static $_collectionName = 'profiles';
	protected static $_documentType = null;
	protected static $_editForm = 'EpicDb_Form_Profile';
	protected static $_documentSetClass = 'EpicDb_Mongo_Profiles';

	public function __construct($data = array(), $config = array())
	{
	  // handled here in order to deal with subclassed resources...
	  if (!is_array($this->_requirements)) $this->_requirements = array();
	  $this->_requirements += array(
			'_lastEditedBy' => array('Document:EpicDb_Mongo_Profile', 'AsReference'),
			'tags' => array('DocumentSet:EpicDb_Mongo_Tags'),
			'stashed' => array('DocumentSet:EpicDb_Mongo_Tags'),
	    );
	  $return = parent::__construct($data, $config);
	}

	/**
	 * The form the record uses
	 *
	 * @var string
	 **/
	protected $_formClass;

	/**
	 *  - Gets the followers that are following record
	 *
	 * @return cursor of users
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function getMyFollowers() {
		return $this->getFollowers($this);
	}
	public static function getFollowers($record, $limit = 9999) {
		return $return = EpicDb_Mongo::db('profile')->fetchAll(array("following" => $record->createReference()), array(), $limit);
	}
	
	public function getParentResource() {
		return new EpicDb_Auth_Resource_Post();
	}

	public function getEditForm() {
		$className = static::$_editForm;
		return new $className(array('profile' => $this));
	}

	public function getPropertyClass($property, $data) {
		if (isset($data['_type'])) {
			return EpicDb_Mongo::dbClass($data['_type']);
		}
	}
	
	// Get rid of me! I suck.
	public function getSocialStats() {
		return null;
	}


	// Returns the string URL of where to load the icon for this
	public function getIcon() {
		if($icon = $this->tags->getTag('icon')) {
			return $icon->getIcon();
		}
		if($this->icon) {
			return $this->icon;
		}
		return "http://s3.r2-db.com/unknown.jpg";
	}

	// Returns the string name of this
	public function getName() {
		if($this->name) return $this->name;
		return "";
	}
	
	// Returns the string description
	public function getDescription() {
		if($this->description) return $this->description;
		return "";
	}
	
	// Returns an array of strings representing view helpers to execute
	public function getTooltipHelpers() {
		return array("icon", "name", "link", "limitDescription");
	}
	
	public function cardProperties($view) {
		return array(
			'is a' => $view->recordTypeLink($this)
		);
	}
	
	public function getFollowedPosts($query = array(), $sort = array("_created" => -1)) {
		// Flag to hide any "deleted" messages
		// var_dump($profile	->export());
		// exit;/
		$query['_deleted'] = array('$exists' => false);
		$query['_created'] = array('$ne' => false);		
		foreach(array($this->following, $this->watching) as $set) {
			foreach($set as $record) { 
				if(!$record) continue;
				$query['$or'][] = array(
					'tags' => array(
						'$elemMatch' => array(
							'ref' => $record->createReference(),
						)
					)
				);
			}			
		}
		foreach($this->blocking as $record) {
			$query['$nor'][] = array(
				'tags' => array(
					'$elemMatch' => array(
						'ref' => $record->createReference(),
					)
				)
			);
		}
		$query['$or'][] = array(
			'tags' => array(
				'$elemMatch' => array(
					'ref' => $this->createReference()
				)
			)
		);
		$query['$nor'][] = array(
			'tags' => array(
				'$elemMatch' => array(
					'reason' => 'author',
					'ref' => $this->createReference()
				)
			)
		);
		$results = EpicDb_Mongo::db('post')->fetchAll($query, $sort);
		return $results;
	}
	
	public function getRouteParams() {
		return array('profile' => $this);
	}
	
	public function getDefaultAction() {
		return static::$_defaultAction;
	}
	
	public function getLayout() {
		return $this->_layout;
	}
	
	public function postSave() {
		// Generate the SearchResult cache
		$keywords = array($this->description, $this->name); 
		$filter = new MW_Filter_Slug();
		$url = "/".$this->_type."/".$this->id."/".$filter->filter($this->name);
		$score = 0;
		if($this->votes && isset($this->votes['score'])) {
			$score = $this->votes['score'];
		} 
		EpicDb_Mongo::db('search')->generate(array(
			'records' => array($this),
			'keywords' => $keywords,
			'name' => $this->name,
			'type' => $this->_type,
			'tags' => $this->tags,
			'icon' => $this->getIcon(),
			'score' => count($this->getMyFollowers()),
			'url' => $url,
		));
		return parent::postSave();
	}
	

} // END class EpicDb_Mongo_Profile