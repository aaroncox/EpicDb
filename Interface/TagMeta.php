<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
interface EpicDb_Interface_TagMeta
{
	// Returns an array of properties to use when tagging
	public function getTagMeta();
	
	// Set tag meta should take a EpicDb_Mongo_Reference
	public function setTagMeta($tag);
} // END class EpicDb_Interface_TagMeta