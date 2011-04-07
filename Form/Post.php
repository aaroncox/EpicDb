<?php
/**
 * R2Db_Form_Message
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Form_Post extends EpicDb_Form
{
	protected $_isNew = false;
	protected $_post = null;
	protected $_parent = null;

	/**
	 * getPost - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function getPost()
	{
		return $this->_post;
	}
	/**
	 * setPost($post) - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function setPost($post)
	{
		$this->_post = $post;
		$this->_isNew = $post->isNewDocument();
		return $this;
	}
	
	protected $_rev = false;
	public function setRev($rev) {
	  $this->_rev = $rev;
	  return $this;
	}
	/**
	 * init - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function init()
	{
		parent::init();
		$post = $this->getPost();
		// var_dump($post); exit;
		if($this->_isNew) {			
			$user = MW_Auth::getInstance()->getUser();
			$post->_profile = $profile = EpicDb_Auth::getInstance()->getUserProfile();
			$post->tags->tag($profile, 'author');
			if($user) {
				$post->grant($user, "edit");
				$post->grant($user, "delete");				
			}
		}
		$this->addElement("markdown", "source", array(
				'order' => 100,
				'required' => true,
				'validators' => array(
					array('StringLength',400,10),
				),
				'class' => 'markDownEditor',
				'label' => 'Post a message',
				'description' => 'When you post on someone else\'s profile, it is considered a \'Message\'. The message will appear publicly on your profile and the recipients profile, but will not be visible on either of your followers profiles.'
			));
		if(!$this->_isNew) {
		  $this->addElement("text", "reason", array(
		    'order' => 1000,
		    'required' => false,
		    'placeholder' => 'Reason for Edit',
		    'label' => 'Reason for Edit',
		    
		  ));
			$this->source->setLabel("Edit Post");
			$source = $post->source;
			if($this->_rev !== false) {
			  $source = $post->revisions[$this->_rev]->source;
  			if (!$source) {
  			  $source = $post->revisions[$this->_rev]->body;
			  }
			  $this->reason->setValue("Roll Back from Revision #".($this->_rev+1));
			}
			if (!$source) {
			  $source = $post->body;
			}
			$this->setDefaults(array("source" => $source, "parent" => $post->_parent->_id));
		}
		
	}
	public function save() {
    $me = MW_Auth::getInstance()->getUserProfile();
		$post = $this->getPost();
		
		if($this->_isNew) {
			$post->grant(MW_Auth::getInstance()->getUser());			
			// If we don't have viewers for some reason, lets make em
		} else {
			EpicDb_Mongo_Revision::makeEditFor($post, $this->reason->getValue());			
		}
		if($this->source) {
			$post->source = $this->source->getValue();
			$post->body = $this->source->getRenderedValue();			
		}		
		if($this->requestType) {
			$post->_requestType = $this->requestType->getValue();
		}
		if($parentId = $this->parent->getValue()) {
			$parent = EpicDb_Mongo::db('posts')->find($parentId)->touch();
			$post->_parent = $parent;
			$parent->bump();
		}

		// var_dump($post); exit;
		return $post->save();
	}
	public function process($data) {
		if($this->isValid($data)) {
			$this->save();
			return true;
		}
		return false;
	}
	public function render() {
		$this->removeDecorator('FloatClear');    
		return parent::render();
	}
} // END class R2Db_Form_Message
