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
	protected $_recordType = 'comment';
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
		$this->removeElement('source');
		$this->removeElement('tags');
		$this->addElement("textarea", "body", array(
				'order' => 100,
				'required' => true,
				'label' => 'Your comment...',
				'description' => '',
				'cols' => 92,
				'rows' => 15,
			));
		$this->setButtons(array("save" => "Post Comment"));
	}
	public function save() {
		$question = $this->getPost();
		$question->body = $this->body->getValue();
		return parent::save();
	}
} // END class EpicDb_Form_Post_Comment
