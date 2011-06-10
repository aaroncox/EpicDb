<?php
/**
 * EpicDb_Mongo_Document
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Document extends MW_Mongo_Document
{
	public static function fetchOne($query) {
		$result = parent::fetchOne($query);
		if(APPLICATION_ENV == 'development') {
			MW_Mongo_Log::log(static::getCollectionName(), $query);
		}
		return $result;
	}
	public static function fetchAll($query = array(), $sort = array(), $limit = false, $skip = false) {
		$result = parent::fetchAll($query, $sort, $limit, $skip);
		if(APPLICATION_ENV == 'development') {
			// $explain = $result->getInnerIterator()->explain(); // Not used yet...
			MW_Mongo_Log::log(static::getCollectionName(), $query, $sort, $limit, $skip);			
		}
		return $result;
	}
} // END class EpicDb_Auth_Mongo_Resource_Document