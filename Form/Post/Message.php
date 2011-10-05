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
	protected $_sourceLabel = "Your Message";
	protected $_editSourceLabel = "Edit Your Message";
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
		$this->removeElement("tags");
		if($post->_parent->id) {
			// Then this is a message responding to a message
		} else {
			// Then this is a new message
			$this->addElement("text", "title", array(
				'order' => 50,
				'validators' => array(
					array('StringLength',120,0),
				),
				'label' => 'Message Title (Optional)',
				'size' => 80,
				'description' => '120 character or less title for your message.'
			));			
			$this->addElement("checkbox", "private", array(
				'order' => 101,
				'label' => 'Is this message Private?',
				'description' => 'Checking this checkbox will cause this message to only appear for you and the recipient.',
			));
			
		}
		$this->setButtons(array("save" => "Post Message"));
	}
	public function getDefaultValues()
	{
		$values = parent::getDefaultValues();
		$data = $this->getInitialData();
		$values['title'] = $data->title;
		return $values;
	}
	public function save() {
		$message = $this->getPost();
		if(!$message->_parent->id) {
			if($this->private->getValue()) {
				$message->_private = true;
			}
			$message->title = $this->title->getValue();			
		}
		return parent::save();
	}
	
} // END class EpicDb_Form_Post_Message
