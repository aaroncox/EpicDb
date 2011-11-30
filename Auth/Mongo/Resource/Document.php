<?php
/**
 * EpicDb_Auth_Mongo_Resource_Document
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Auth_Mongo_Resource_Document extends MW_Auth_Mongo_Resource_Document
{
	public static function fetchOne($query) {
		$result = parent::fetchOne($query);
		if(APPLICATION_ENV == 'development') {
			MW_Mongo_Log::log(static::getCollectionName(), null, $query);
		}
		return $result;
	}
	public static function fetchAll($query = array(), $sort = array(), $limit = false, $skip = false) {
		if(!isset($query['_deleted'])) {
			// This should check their rep level eventually, for now, we check if they are moderator.
			if(!EpicDb_Auth::getInstance()->hasPrivilege(new EpicDb_Auth_Resource_Moderator())) {
				$query += array('_deleted' => array('$exists' => false));				
			}
		}
		$result = parent::fetchAll($query, $sort, $limit, $skip);
		if(APPLICATION_ENV == 'development') {
			// $explain = $result->getInnerIterator()->explain();
			MW_Mongo_Log::log(static::getCollectionName(), $query, $sort, $limit, $skip);			
		}
		return $result;
	}
	public function save() {
		if($this instanceOf EpicDb_Interface_Searchable) {
			$cache = $this->getSearchCache();
			$query = array(
				'record' => $this->createReference(),
				'seed' => array(
					'$exists' => false,
				),
			);
			$doc = EpicDb_Mongo::db('search')->fetchOne($query);
			if(!$doc) {
				$doc = EpicDb_Mongo::newDoc('search');
				$doc->record = $this;
			}
			$references = array();
			foreach($cache as $key => $value) {
				if($key == 'tags') {
					foreach($value as $tag) {
						$references[] = $tag->ref->name;
					}
					$doc->keywordReferences = $references;
				} else {
					$doc->$key = $value;					
				}
			}
			// var_dump($references); exit;
			$doc->save();
		}
		return parent::save();
	}
} // END class EpicDb_Auth_Mongo_Resource_Document