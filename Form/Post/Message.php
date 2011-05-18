<?php
/**
 * EpicDb_Form_Post_Message
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Form_Post_Message extends EpicDb_Form_Post
{
	protected $_isNew = false;
	protected $_record = null;
	protected $_recordType = 'message';
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
			$this->source->setLabel("Your Message");
			$this->setButtons(array("save" => "Post Message"));
		}
	} // END class EpicDb_Form_Post_Message
