<?php
/**
 * EpicDb_Mongo_Record_Group_Website
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 **/
class EpicDb_Mongo_Profile_Group_Website extends EpicDb_Mongo_Profile_Group
{
	public $contextHelper = 'websiteContext';
	protected static $_documentType = "website";
	protected static $_editForm = 'EpicDb_Form_Profile_Group_Website';
	protected static $_defaultAction = 'news'; // You can override this to cause the default action on specific profiles to change.
	
	public function isAdmin() {}
	public function isMe() {}
	
	public function getFollowing($limit = 25, $params = array()) {
		return array();
	}
	
	public function getMembers($limit = false, $params = array()) {
		$count = $this->admins->count();
		if ($count == 0) return array();
		$indexes = range(0, $count-1);
		shuffle($indexes);
		if($limit) $indexes = array_slice($indexes, 0, $limit);
		$return = array();
		foreach($indexes as $idx) {
			$return[] = $this->admins[$idx];
		}
		// var_dump($return); exit;
		return $return;
	}
	
	public function getParentResource() {
		return new EpicDb_Auth_Resource_Website();
	}
	
	public function getAuthoredPosts($query = array(), $sort = array("_created" => -1), $limit = false) {
		$query['_publish'] = array(
			'$lt' => time()
		);
		$query['$and'][] = array(
			'tags.ref' => $this->createReference(),
			'tags.reason' => 'source'
		);
		return EpicDb_Mongo::db('post')->fetchAll($query, $sort, $limit);
	}
}