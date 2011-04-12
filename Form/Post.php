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
		$this->addElement("markdown", "source", array(
				'order' => 100,
				'required' => true,
				'class' => 'markDownEditor',
				'label' => 'Post Message',
				'description' => '',
				'cols' => 92,
				'rows' => 15,
			));
		$this->addElement("tags", "tags", array(
			'order' => 150,
			'required' => true,
			'label' => 'Tags',
			'description' => 'Tagging questions helps categorize them, making it easier to find questions based on specific topics. To use the tagging engine, simply start typing what you are looking for in the search box, and click on the tag that matches the topics your question is related to.',
		));
		if($this->_isNew) {
			// If we have a new post, lets establish a few things
			$profile = MW_Auth::getInstance()->getUserProfile();
			// grant the default permissions to this post.
			$post->grant($profile->user);
			// $post->grant(MW_Auth_Group_Super::getInstance());
			// $post->grant(MW_Auth_Group_User::getInstance(), "comment");
			// $post->grant(MW_Auth_Group_User::getInstance(), "answer");
			// Tag the author as the author
			$post->tags->tag($profile, 'author');
		} else {
			// Add a reason for your edit
			$this->addElement("text", "reason", array(
				'order' => 1000,
				'required' => false,
				'placeholder' => 'Reason for Edit',
				'label' => 'Reason for Edit',

			));
			// Change the label to edit post
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
			$this->setDefaults(array(
				"source" => $source,
				"tags" => $post->tags->getTags('tag'),
			));
		}
		$this->setButtons(array("save" => "Post"));

	}
	public function save() {
		$me = EpicDb_Auth::getInstance()->getUserProfile();
		$post = $this->getPost();
		if(!$this->_isNew) {
			$post->bump($me);
			EpicDb_Mongo_Revision::makeEditFor($post, $this->reason->getValue());
		} else {
			$post->_created = time();
		}
		if($this->source) {
			$post->source = $this->source->getValue();
			$post->body = $this->source->getRenderedValue();
		}
		$filter = new EpicDb_Filter_TagJSON();
		if ($this->tags) {
			$post->tags->setTags($filter->toArray($this->tags->getValue()),'tag');
		}
		if($this->requestType) {
			$post->_requestType = $this->requestType->getValue();
		}
		if($post->_parent && $post->_parent->export() != array()) {
			$post->_parent->bump($me);
		}
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
