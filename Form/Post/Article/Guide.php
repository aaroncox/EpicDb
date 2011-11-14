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
			'recordType' => 'class,advanced-class',
		));
		$this->addElement('checkbox', 'published', array(
			'order' => 11,
			'label' => 'Publish to the Guide Index',
			'description' => 'Click this checkbox and save when you are ready to publish this guide to the public.',
			'value' => $post->_published,
		));
		$this->tags->setDescription("Please tag up to 8 relevant records from the database to your guide.")->setLabel('Related Tags');
		$this->setDefaults( $this->getDefaultValues() );
		$this->setButtons(array("save" => "Post News"));
	}
	public function getDefaultValues()
	{
		$values = parent::getDefaultValues();
		$post = $this->getPost();
		$values['class'] = $post->class->getTags('tag');
		return $values;
	}
	public function save() {
		parent::save();
		$post = $this->getPost();
		if($classes = $this->class->getTags()) {
			$post->class->setTags('tag', $classes);			
		}
		$post->_published = (bool) $this->published->getValue();
		$post->title = $this->title->getValue();
		$filter = new MW_Filter_Slug();
		$post->slug = $filter->filter($post->title);
		$post->save();
		// var_dump($post->export()); exit;
		return $post->save();
	} 
} // END class EpicDb_Form_Post_Article_Guide extends EpicDb_Form_Post_Article