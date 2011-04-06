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
class EpicDb_Mongo_Record extends MW_Mongo_Document
{
	protected static $_collectionName = 'records';
  protected static $_documentType = null;
  protected static $_documentSetClass = 'EpicDb_Mongo_Records';
	
	/**
	 * __construct - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function __construct($data = array(), $config = array())
	{
		$this->addRequirements(array(
			'_lastEditedBy' => array('Document:EpicDb_Mongo_Profile', 'AsReference'),
			'revisions' => array('DocumentSet'),
			'revisions.$' => array('Document:EpicDb_Mongo_Revision'),

		));
		return parent::__construct($data, $config);
	}
	
	public function getPropertyClass() {
		if (isset($data['_type'])) {
	    return EpicDb_Mongo::db($data['_type']);
	  }
	}

} // END class EpicDb_Mongo_Record