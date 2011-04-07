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
				'label' => 'Title',
				'description' => '120 character description of your question.'
			));
		$this->setButtons(array("save" => "Post"));
		$this->source->setLabel("The Long Version")->setDescription("Asking a question on R2-Db is as simple as providing a short version of the question and then your full blown question in the 'Long Version'. Both fields are required, and both fields may be edited by the community to make the question more clear. For more information, please visit our FAQ page on Questions.");
		if(!$this->_isNew) {
			$this->setDefaults(array("tldr" => $question->tldr, "body" => $question->body, "parent" => $question->_parent->_id));
		}
		// var_dump($this->_elements); exit;
	}
	public function save() {
		$question = $this->getPost();
		$question->title = $this->tldr->getValue();
		return parent::save();
	}
} // END class R2Db_Form_Post_Comment
