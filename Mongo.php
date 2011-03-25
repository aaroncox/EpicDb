<?php
/**
 * R2Db_Mongo
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo
{
	public static function db($type) {
		return R2Db_Mongo_Schema::getInstance()->getCollectionForType($type);
	}
} // END class R2Db_Mongo