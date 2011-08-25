<?php
/**
 * EpicDb_Mongo_Profile_FollowCount
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Cache_SearchLog extends MW_Auth_Mongo_Resource_Document
{
	protected static $_collectionName = 'searchlog_cache';
	protected static $_documentType = null;
	
	public function fetchPopular($limit) {
		$query = array();
		$sort = array('value' => -1);
		return static::fetchAll($query, $sort, $limit);
	}
}