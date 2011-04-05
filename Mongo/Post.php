<?php
/**
 * EpicDb_Mongo_Post
 *
 * Post Mongo Object
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 **/
class EpicDb_Mongo_Post extends MW_Auth_Mongo_Resource_Document
{
	protected static $_collectionName = 'posts';
  protected static $_documentType = null;
  protected static $_documentSetClass = 'EpicDb_Mongo_Posts';
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
			'_profile' => array('Document:EpicDb_Mongo_Profile', 'AsReference', 'Required'),
			'tags' => array('DocumentSet:EpicDb_Mongo_Tags', 'Required'),
			'revisions' => array('DocumentSet'),
			'revisions.$' => array('Document:EpicDb_Mongo_Revision'),
		));
		return parent::__construct($data, $config);
	}
	
	public function getPropertyClass() {
		if (isset($data['_type'])) {
	    return EpicDb_Mongo::db($data['_type']);
	  }
	}
	
	public function getProfileFeed($profile, $query = array(), $sort = array("_created" => -1), $limit = 20, $skip = false) {
		// First off, lets get the user so we can do some logic based on whether its you or not.
		$user = MW_Auth::getInstance()->getUser();
		// $myFeed = true/false whether its my feed or not
		$myFeed = ($user && $profile->user) ? ($user->createReference() == $profile->user->createReference()):false;
		// Flag to hide any "deleted" messages
		$query['_deleted'] = array('$exists' => false);
		// Flag to hide any "responses", since they will load on the parent
		$query["_parent"] = array('$exists' => false);
		// Show any messages that I have posted
		$query['$or'][] = array('_profile' => $profile->createReference());
		// Show any messages that are directed at me
		$query['$or'][] = array(
			'tags' => array(
				'$elemMatch' => array(
					'ref' => $profile->createReference(),
					'reason' => 'subject',
				)
			)
		);
		// Show any messages that are directed at me
		foreach(EpicDb_Auth::getInstance()->getUserRoles() as $role) {
			$roles[] = $role->createReference();
		}
		$query['_viewers'] = array('$in' => $roles);

		// Switch by Type for the proper feed configuration
		switch($profile->_type) {
			case "wall":
			case "website":
			case "guild":
				// Check the profile's RSS feed cache
				// var_dump($profile->feed, $profile->crawledFeed); exit;
				if($profile->feed && (!$profile->crawledFeed OR $profile->crawledFeed+86400 <= time())) {
					// echo "NEED TO CRAWL [".$profile->feed."]"; exit;
					$profile->crawlFeed();
				}
				// If the type isn't set, lets default to news/articles, or 'article' type
				if(!isset($query['_type'])) {
					$query['_type'] = 'article';					
				}
				break;
			case "group":
			case "profile":
				// If it's my feed, and if I am following people, then $following is an export of who I'm following
				if($myFeed && $profile->following && $following = $profile->following->export()) {			
					foreach($following as $idx => $follow) {
						if(!$follow) unset($following[$idx]);
					}
					// Show messages directed at people I'm following, disabled, not sure we need that.
					// $query['$or'][] = array('_record' => array('$in' => $following)); // Dont think this is needed?
					// Show messages originating from people I'm following, this ones good.
					$query['$or'][] = array(
						'_profile' => array('$in' => $following),
						// '_type' => array('$ne' => 'system')
					);
				}	else {
					// If it's not your feed, this is where we can hide a few things
					// $query['_type'] = array('$ne' => 'system'); // This line breaks the filters :(
				}
				if(!$myFeed && !isset($query['_type'])) {
					$query['_type'] = array('$ne' => 'wall');
				}
				break;
			default:
				// Let me know!
				throw new Exception("No feed generator for this type: [".$profile->_type."]");
		}
		// var_dump($query); exit;
		// Grab the results and return them to the controller
		// unset($query['_typ'])
		$results = EpicDb_Mongo::db('posts')->fetchAll($query, $sort, $limit, $skip);
		// var_dump($results); exit;
		// var_dump($results->export(), $query, $sort, $limit); exit;
		// foreach($results as $idx => $result) {
		// 	if($result->id == "200") {				
		// 		foreach($result->_viewers as $viewer) {
		// 			// var_dump($viewer->export());
		// 		}
		// 		// var_dump($result->export()); exit;
		// 	}
		// }
		// var_dump($query, $results->export()); exit;
		return $results;
	}
	
	public function findResponses($limit = 10, $query = array(), $sort = array()) {
		$query = array(
			"_parent" => $this->createReference(),
			'_deleted' => array(
					'$exists' => false
				)
		);
		$sort = array("_created" => 1);
		return $results = EpicDb_Mongo::db('comment')->fetchAll($query, $sort, $limit);
	}
} // END class EpicDb_Mongo_Post