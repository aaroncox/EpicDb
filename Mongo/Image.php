<?php
/**
 * EpicDb_Mongo_Image
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 **/
class EpicDb_Mongo_Image extends EpicDb_Auth_Mongo_Resource_Document
{
  protected static $_collectionName = 'images';
	protected static $_documentType = 'image';

	/**
	 * __construct - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function __construct($data = array(), $config = array())
	{
		$this->addRequirements(array(
			'tags' => array('DocumentSet:EpicDb_Mongo_Tags'),
			'_owner' => array('Document:EpicDb_Mongo_Profile_User', 'AsReference'),
			'_parent' => array('Document:EpicDb_Mongo_Image', 'AsReference'),
		));
		return parent::__construct($data, $config);
	}
}