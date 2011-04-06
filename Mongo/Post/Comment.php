<?php
/**
 * EpicDb_Mongo_Post_Comment
 *
 * Comment (Post) Mongo Object
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 **/
class EpicDb_Mongo_Post_Comment extends EpicDb_Mongo_Post
{
	protected static $_collectionName = 'posts';
  protected static $_documentType = 'comment';
	/**
	 * __construct - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function __construct($data = array(), $config = array())
	{
		// $this->addRequirements(array(
		// 	'revisions' => array('DocumentSet'),
		// 	'revisions.$' => array('Document:EpicDb_Mongo_Revision'),
		// ));
		return parent::__construct($data, $config);
	}
	
} // END class EpicDb_Mongo_Post