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
class EpicDb_Form_Post_Article extends EpicDb_Form_Post
{
	protected $_isNew = false;
	protected $_profile = null;
	protected $_recordType = 'article';
	
	protected $_sourceLabel = "News";
	public function setProfile($profile) {
		if(!$profile instanceOf EpicDb_Mongo_Profile) throw new Exception("This isn't a profile");
		$this->_profile = $profile;
	}
	public function getProfile() {
		if($this->_profile) return $this->_profile;
		throw new Exception("Profile not set within the article.");
	}
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
		$this->addElement("text", "title", array(
				'order' => 50,
				'required' => true,
				'validators' => array(
					array('StringLength',120,10),
				),
				'label' => 'Title',
				'size' => 80,
				'description' => '120 character or less title (required).'
			));
		$this->tags->setDescription("You are allowed to tag your article with things contained on R2-Db.com to provide relevance to those topics.");
		$this->setButtons(array("save" => "Post News"));
	}
	public function getDefaultValues()
	{
		$values = parent::getDefaultValues();
		$data = $this->getInitialData();
		$values['title'] = $data->title;
		return $values;
	}
	public function save() {
		parent::save();
		$post = $this->getPost();
		$profile = $this->getProfile();
		$post->tags->setTag('author', $profile);
		$post->title = $this->title->getValue();
		return $post->save();
	} 
} // END class EpicDb_Form_Post_Comment
