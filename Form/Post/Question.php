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
				'description' => '120 character description of your question.'
			));
		$this->setButtons(array("save" => "Post"));
		if(!$this->_isNew) {
			$this->source->setLabel("Edit Post");
			$this->setDefaults(array("title" => $question->title, "body" => $question->body, "parent" => $question->_parent->_id));
		} else {
			$this->source->setLabel("Your Answer")->setDescription("Do you have the answer to this question? Post your answer to earn achievements and reputation on EpicAdvice.com!");
		}
		// var_dump($this->_elements); exit;
	}
	public function save() {
		$question = $this->getPost();
		$question->title = $this->title->getValue();
		return parent::save();
	}
} // END class R2Db_Form_Post_Comment
