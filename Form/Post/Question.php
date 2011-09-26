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
class EpicDb_Form_Post_Question extends EpicDb_Form_Post
{
	protected $_isNew = false;
	protected $_record = null;
	protected $_recordType = 'question';

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
				'label' => 'Title of Question',
				'size' => 80,
				'description' => '120 character or less description of your question.'
			));
		$this->addElement("checkbox", "community", array(
			'label' => 'Community Post?',
			'description' => 'Designate this question as a "Community Post", for more information read the Q&A FAQ.',
			'order' => 200,
		));
		$this->setButtons(array("save" => "Post"));
		if(!$this->_isNew) {
			$this->source->setLabel("The Question")->setDescription("Please be as descriptive as possible when asking your question, include as many details as possible, and don't hit people in the face with a wall of text in one huge paragraph.");
			$this->setDefaults(array("title" => $question->title, "body" => $question->body, "parent" => $question->_parent->_id));
			$this->setButtons(array("save" => "Post Question"));
		} else {
			$this->source->setLabel("The Question")->setDescription("Please be as descriptive as possible when asking your question, include as many details as possible, and don't hit people in the face with a wall of text in one huge paragraph.");
			$this->setButtons(array("save" => "Post Question"));
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
		$question = $this->getPost();
		$question->title = $this->title->getValue();
		$question->disableRep = $this->community->getValue();
		return parent::save();
	}
} // END class R2Db_Form_Post_Comment
