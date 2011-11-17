<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Form_Console_TagScreenshot extends EpicDb_Form_Console
{
	private $_image = null;
	/**
	 * getPost - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function getImage()
	{
		if (!$this->_image instanceOf EpicDb_Mongo_Image) throw new Exception("Invalid Image");
		return $this->_image;
	}

	/**
	 * setPost($post) - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function setImage($image)
	{
		$this->_image = $image;
		return $this;
	}
	public function init() {
		parent::init();
		$this->addElement('tags', 'tags', array(
			'order' => 130,
			'label' => 'Tags',
			'description' => 'What is this screenshot related to in the database?.',
		));
		$this->setButtons(array("save" => "Update Image"));
	}
	public function getDefaultValues()
	{
		$values = parent::getDefaultValues();
		$image = $this->getImage();
		$values['tags'] = $images->tags->getTags();
		return $values;
	}
	public function save() {
		$image = $this->getImage();
		$filter = new EpicDb_Filter_TagJSON();
		if ($this->tags) {
			$image->tags->setTags($this->tags->getTags(),'tag');
		}
		return $image->save();
	}
} // END class EpicDb_Form_Console_TagScreenshot extends EpicDb_Form_Console