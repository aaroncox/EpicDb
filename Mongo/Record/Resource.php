<?php
/**
 * EpicDb_Mongo_Record_Resource
 *
 * Resources in a game (currencies, crafting mats, mana, etc... expendable, stackable, usable resources)
 *  -- Used in EpicDb_Mongo_Meta_Cost
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Record_Resource extends EpicDb_Mongo_Record
{
	protected static $_collectionName = 'records';
	protected static $_documentType = 'resource';
	
	/**
	 *  grab - attempts to grab a resource type and creates a record if it can't find it... resources should be auto generated
	 *  	- only use grab if needed...
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public static function grab($slug) {
		$query = array(
			'slug' => $slug
		);
		$resource = static::fetchOne($query);
		if(!$resource) {
			$resource = new static;
			$resource->slug = $slug;
			$resource->save();
		}
		return $resource;
	}
} // END class EpicDb_Mongo_Record_Tag