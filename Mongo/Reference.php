<?php
/**
 * EpicDb_Mongo_Reference
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Reference extends MW_Mongo_Document
{
	protected $_requirements = array(
	    'ref' => array('Document:MW_Mongo_Document', 'AsReference', 'Required'), 
	  );

	public function set(MW_Mongo_Document $ref) {
		$this->ref = $ref;
	}

	public function getPropertyClass($property, $data)
	{
	  if ($property == 'ref') {
	    return R2Db_Mongo::getClassNameForType($data['_type']);
	  }
	}
	
} // END class EpicDb_Mongo_Reference

