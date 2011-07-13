<?php
/**
 * R2Db_Form_Post_Comment
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Form_Post_Comment extends EpicDb_Form_Post
{
	protected $_isNew = false;
	protected $_record = null;
	protected $_recordType = 'comment';
	
	protected $_sourceLabel = "Leave a Comment";
	
	/**
	 * init - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function init()
	{
		$post = $this->getPost();
		parent::init();
		$this->removeElement('tags');
		$this->setButtons(array("save" => "Post Comment"));
	}
} // END class EpicDb_Form_Post_Comment
