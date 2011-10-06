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
	protected $_sourceLabel = "Message Body";
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
				'label' => 'Message Title',
				'required' => true,
				'size' => 80,
				'description' => '120 character or less title for your message.'
			));			
			$this->addElement("checkbox", "private", array(
				'order' => 101,
				'label' => 'Is this Private?',
				'description' => 'Checking this checkbox will cause this message to only appear for you and the recipient.',
			));				
			$existingSubjects = $post->tags->getTags("subject");
			$this->addElement("tags", "subjects", array(
				'order' => 150,
				'required' => true,
				'label' => 'Message Recipients...',
				'recordType' => 'user',
				'description' => 'To send a message to specific users, please find them using the search box above and click on the users you wish to send this message to.',
				'value' => $existingSubjects,
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
		// If the title field exists, save it onto the post.
		if($this->title) {
			$message->title = $this->title->getValue();			
		}
		// Take all users specified in the subject tagset
		if($this->subjects && $profiles = $this->subjects->getTags()) {
			// Set them as a tag of subject
			$message->tags->setTags('subject', $profiles); 
			foreach($profiles as $profile) {
				// Grant all subjects "view" permissions
				$message->grant($profile->user, 'view'); 
			}
		}
		// If the private flag exists and is set to true, set this message to private.
		if($this->private && $this->private->getValue()) {
			// Set the private flag on the post
			$message->_private = true;
			// Grant permission to the author of the post.
			$message->grant($message->tags->getTag('author')->user, 'view');
		}
		// If the message's parent is private, this should be too.
		if($message->_parent && $message->_parent->_private) {
			// Set the private flag on the post
			$message->_private = true;
			// Grant permission to the author of the parent's post.
			$message->grant($message->_parent->tags->getTag('author')->user);
			foreach($message->_parent->tags->getTags("subject") as $subject) {
				// Grant permission to all the subjects of the original post.
				$message->grant($subject->user, 'view');
			}				
		}
 		return parent::save();
	}
	
} // END class EpicDb_Form_Post_Message
