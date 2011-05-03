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
	protected $_version = 3;
  protected $_tag = 'epicdb';
	protected $_classMap = array(
		// Profile Types
		'profile' => 'EpicDb_Mongo_Profile',
		'user' => 'EpicDb_Mongo_Profile_User',
		'group' => 'EpicDb_Mongo_Profile_Group',
		'website' => 'EpicDb_Mongo_Profile_Group_Website',
		// Record Types
		'tag' => 'EpicDb_Mongo_Record_Tag',
		// Post Types
		'post' => 'EpicDb_Mongo_Post',
		'question' => 'EpicDb_Mongo_Post_Question',
		'question-comment' => 'EpicDb_Mongo_Post_Question_Comment',
		'answer' => 'EpicDb_Mongo_Post_Question_Answer',
		'comment' => 'EpicDb_Mongo_Post_Comment',
		'article-rss' => 'EpicDb_Mongo_Post_Article_RSS',
		'wiki' => 'EpicDb_Mongo_Wiki',
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
			case 2:
				$db->execute("db.posts.ensureIndex({'tags.ref':1, 'tags.reason': 1})");
				$db->execute("db.posts.ensureIndex({'votes.score':1})");
				$db->execute("db.posts.ensureIndex({_created:1, touched:1})");
				$db->execute("db.posts.ensureIndex({_parent:1})");
				$db->execute("db.posts.ensureIndex({_parent:1, 'score.accepted':1})");
    }
	}
}