<?php
/**
 * EpicDb_Mongo_Post
 *
 * Post Mongo Object
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 **/
class EpicDb_Mongo_Post extends MW_Auth_Mongo_Resource_Document
{
	protected static $_collectionName = 'posts';
  protected static $_documentType = null;
  protected static $_documentSetClass = 'EpicDb_Mongo_Posts';
	/**
	 * __construct - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function __construct($data = array(), $config = array())
	{
		$this->addRequirements(array(
			'_viewers' => array("DocumentSet"),
			'_viewers.$' => array('Document:MW_Auth_Mongo_Role', 'AsReference'),
			// '_record' => array('Document:R2Db_Mongo', 'AsReference'),
			'_parent' => array('Document:EpicDb_Mongo_Post', 'AsReference'),
			'_lastEditedBy' => array('Document:EpicDb_Mongo_Profile', 'AsReference'),
			'_profile' => array('Document:EpicDb_Mongo_Profile', 'AsReference', 'Required'),
			'tags' => array('DocumentSet:EpicDb_Mongo_Tags', 'Required'),
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
} // END class EpicDb_Mongo_Post