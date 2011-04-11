<?php
/**
 * EpicDb_Mongo_Schema
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Schema extends MW_Mongo_Schema {
	protected $_version = 2;
  protected $_tag = 'epicdb';
	protected $_classMap = array(
		// Profile Types
		'profile' => 'EpicDb_Mongo_Profile',
		'user' => 'EpicDb_Mongo_Profile_User',
		'group' => 'EpicDb_Mongo_Profile_Group',
		// Record Types
		'tag' => 'EpicDb_Mongo_Record_Tag',
		// Post Types
		'post' => 'EpicDb_Mongo_Post',
		'question' => 'EpicDb_Mongo_Post_Question',
		'question-comment' => 'EpicDb_Mongo_Post_Question_Comment',
		'answer' => 'EpicDb_Mongo_Post_Question_Answer',
		'comment' => 'EpicDb_Mongo_Post_Comment',
		// votes
		'vote' => 'EpicDb_Mongo_Vote',

	);
	
	/**
	* Class Instance - Singleton Pattern
	*
	* @var self
	**/
	static protected $_instance = NULL;

	/**
	* Returns (or creates) the Instance - Singleton Pattern
	*
	* @return self
	* @author Corey Frang
	**/
	static public function getInstance()
	{
	 if (static::$_instance === NULL) {
	   static::$_instance = new static();
	 }
	 return static::$_instance;
	}
	
	public function updateFrom($version)
  {
    $db = self::getMongoDb();
    switch($version) {
      case 0:
				$this->getCollectionForType('profile')->remove(array('_type' => array('$exists' => false)));
			case 1:
        $db->execute('db.posts.ensureIndex({id:1, _type:1}, {unique: true})');
    }
	}
}