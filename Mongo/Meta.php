<?php
/**
 * EpicDb_Mongo_Meta
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Meta extends MW_Mongo_Document
{
	public function init() {
		$this->addRequirements(EpicDb_Mongo::db('metaKeys')->getRequirementsArray());
	}
} // END class EpicDb_Mongo_Tags