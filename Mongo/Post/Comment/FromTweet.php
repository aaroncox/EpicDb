<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Mongo_Post_Comment_FromTweet extends MW_Mongo_Document
{	
	protected static $_collectionName = 'tweets';
	
	protected $_requirements = array(
		'_parent' => array('Document:EpicDb_Mongo_Post', 'AsReference'),
		'_deletedBy' => array('Document:EpicDb_Mongo_Profile_User', 'AsReference'),
		'tags' => array('DocumentSet:EpicDb_Mongo_Tags'),
	);
	
	public function populate($tweetObj) {
		$this->display_name = (string) $tweetObj->user->name;
		$this->screen_name = (string) $tweetObj->user->screen_name;
		$this->avatar = (string) $tweetObj->user->profile_image_url;
		$this->body = (string) $tweetObj->text; 
		$this->_created = strtotime((string) $tweetObj->created_at);
		if($tweetObj->in_reply_to_status_id) {
			$parent = EpicDb_Mongo::db('post')->fetchOne(array('_twitterId' => new MongoInt64($tweetObj->in_reply_to_status_id)));
			if($parent) {
				$this->tags->tag($parent, "parent");
				$this->_parent = $parent;
			}
		}
		$this->save();
	}
} // END class EpicDb_Mongo_Post_Comment_FromTweet