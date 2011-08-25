<?php
/**
 * EpicDb_Mongo_Record
 *
 * undocumented class
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_SearchLog extends MW_Mongo_Document
{
	protected static $_collectionName = 'searchlog';
	protected static $_documentType = null;
	
	public function log($query) {
		$entry = new self();
		$entry->query = trim($query);
		$entry->time = time();
		$res = $entry->save();			
	}
	
	public function popular() {
		
	}
}