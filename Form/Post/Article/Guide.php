<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Form_Post_Article_Guide extends EpicDb_Form_Post_Article
{
	protected $_isNew = false;
	protected $_profile = null;
	protected $_sourceLabel = "Content";
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
		$this->addElement('tags', 'class', array(
			'order' => 130,
			'label' => 'Specific Class',
			'description' => 'What class is this guide about (if any)? You are allowed to tag a combination of 2x Advanced Classes or Classes.',
			'limit' => 2,
			'recordType' => 'class, advanced-class',
		));
		$this->tags->setDescription("Please tag up to 8 relevant records from the database to your guide.")->setLabel('Related Tags');
		$this->setButtons(array("save" => "Post News"));
	}
	public function getDefaultValues()
	{
		$values = parent::getDefaultValues();
		$data = $this->getInitialData();
		// $values['title'] = $data->title;
		return $values;
	}
	public function save() {
		parent::save();
		
		$post = $this->getPost();
		// var_dump($post); exit;
		if($classes = $this->class->getTags()) {
			$post->class->setTags('tag', $classes);			
		}
		// $post->title = $this->title->getValue();
		return $post->save();
	} 
} // END class EpicDb_Form_Post_Article_Guide extends EpicDb_Form_Post_Article