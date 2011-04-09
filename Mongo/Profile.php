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
class EpicDb_Mongo_Profile extends MW_Auth_Mongo_Resource_Document implements EpicDb_Interface_Cardable
{
	protected static $_collectionName = 'profiles';
	protected static $_documentType = null;

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
	public static function getFollowers($record, $limit = 9999) {
		return $return = EpicDb_Mongo::db('profile')->fetchAll(array("following" => $record->createReference()), array(), $limit);
	}

	public function getForm($options = array()) {
		$class = $this->_formClass;
		if(!$class) {
			throw new Exception("No form defined for this profile type [".get_class($this)."].");
		}
		Zend_Loader::loadClass($class);
		return new $class(array("profile" => $this)+$options);
	}

	// Get rid of me! I suck.
	public function getSocialStats() {
		return null;
	}

	public function getIcon() {
		if($this->icon) {
			return $this->icon;
		}
		return "/images/icons/unknown.jpg";
	}

	public function cardProperties($view) {
		return array(
			'is a' => $view->recordTypeLink($this)
		);
	}

} // END class EpicDb_Mongo_Profile