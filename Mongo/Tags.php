<?php
/**
 * EpicDb_Mongo_Tags
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Tags extends MW_Mongo_DocumentSet_Tags
{
	protected function _resolve( $tag ) {
		$ref = $tag->ref;
		if($ref instanceOf EpicDb_Interface_TagMeta) $ref = $ref->setTagMeta($tag);
		return $ref;
	}
} // END class EpicDb_Mongo_Tags