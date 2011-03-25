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
class EpicDb_Mongo_Profile extends MW_Auth_Mongo_Resource_Document
{
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
		return $return = EpicDb_Mongo::db('user')->fetchAll(array("following" => $record->createReference()), array(), $limit);
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
} // END class EpicDb_Mongo_Profile