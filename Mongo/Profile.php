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
class EpicDb_Mongo_Profile extends EpicDb_Auth_Mongo_Resource_Document implements EpicDb_Interface_Cardable, EpicDb_Interface_Tooltiped
{
	public $summaryHelper = 'profileSummary';
	public $contextHelper = 'profileContext';
	
	protected static $_collectionName = 'profiles';
	protected static $_documentType = null;
	protected static $_editForm = 'EpicDb_Form_Profile';
	protected static $_documentSetClass = 'EpicDb_Mongo_Profiles';

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
		if($this->icon) {
			return $this->icon;
		}
		return "/images/icons/unknown.jpg";
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
		foreach($this->following as $record) { 
			if(!$record) continue;
			$query['$or'][] = array(
				'tags' => array(
					'$elemMatch' => array(
						'ref' => $record->createReference(),
					)
				)
			);
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
					'reason' => 'tag',
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
		// Make sure I have the permissions to view this post
		foreach(EpicDb_Auth::getInstance()->getUserRoles() as $role) {
			$roles[] = $role->createReference();
		}
		$query['_viewers'] = array('$in' => $roles);

		$results = EpicDb_Mongo::db('post')->fetchAll($query, $sort);
		return $results;
	}
	

} // END class EpicDb_Mongo_Profile