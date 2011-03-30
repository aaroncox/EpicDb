<?php
/**
 * EpicDb_Mongo_Posts
 *
 * DocumentSet for different kinds of posts
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Posts extends Shanty_Mongo_DocumentSet
{
	public function getPropertyClass($property, $data)
	{
	  if (isset($data['_type'])) {
	    return EpicDb_Mongo::dbClass($data['_type']);
	  }
	}
} // END class EpicDb_Mongo_Posts