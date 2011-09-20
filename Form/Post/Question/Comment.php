<?php
/**
 * EpicDb_Form_Post_Comment
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Form_Post_Question_Comment extends EpicDb_Form_Post
{
	protected $_isNew = false;
	protected $_recordType = 'question-comment';
	protected $_parent = false;
	/**
	 * init - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function init()
	{
		$question = $this->getPost();
		parent::init();
		$this->removeElement('tags');
		$this->setButtons(array("save" => "Post Comment"));
	}
	
} // END class EpicDb_Form_Post_Comment
