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
class EpicDb_Mongo_Cache_Followers extends MW_Auth_Mongo_Resource_Document
{
	protected static $_collectionName = 'follows_cache';
	protected static $_documentType = null;
	
	public function getPropertyClass($property, $data) {
		if (isset($data['ref']) && isset($data['refType'])) {
			return EpicDb_Mongo::dbClass($data['refType']);
		}
	}
}