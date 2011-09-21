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
class EpicDb_Form_Post_Poll extends EpicDb_Form_Post
{
	protected $_isNew = false;
	protected $_record = null;
	protected $_recordType = 'poll';

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
		$this->addElement("text", "title", array(
				'order' => 50,
				'required' => true,
				'validators' => array(
					array('StringLength',120,10),
				),
				'label' => 'Title of Poll',
				'size' => 80,
				'description' => '120 character or less description of your pol.'
			));
		$this->setButtons(array("save" => "Post"));
		if(!$this->_isNew) {
			$this->source->setLabel("The Poll")->setDescription("Do you have the answer to this question? Post your answer to earn achievements and reputation on EpicAdvice.com!");
			$this->setDefaults(array("title" => $question->title, "body" => $question->body, "parent" => $question->_parent->_id));
			$this->setButtons(array("save" => "Post Update"));
		} else {
			$this->source->setLabel("Poll Description")->setDescription("Please be as descriptive as possible when stating the objective of your poll, include as many details as possible, and don't hit people in the face with a wall of text in one huge paragraph.");
			$this->setButtons(array("save" => "Post your Poll"));
		}
		// var_dump($this->_elements); exit;
	}
	public function getDefaultValues()
	{
		$values = parent::getDefaultValues();
		$data = $this->getInitialData();
		$values['title'] = $data->title;
		return $values;
	}
	public function save() {
		$poll = $this->getPost();
		$poll->title = $this->title->getValue();
		return parent::save();
	}
} // END class R2Db_Form_Post_Comment
