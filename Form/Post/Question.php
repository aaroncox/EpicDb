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
		if(EpicDb_Auth::getInstance()->hasPrivilege(new EpicDb_Auth_Resource_Moderator())) {
			$this->addElement('checkbox', 'featured', array(
				'order' => 12,
				'label' => 'Feature on the Homepage?',
				'description' => 'Click this checkbox to feature this question on the homepage.',
				'value' => (bool) $post->_featured,
			));			
			$this->addElement('text', 'featuredDescription', array(
				'order' => 13,
				'label' => 'Question Description for Homepage',
				'description' => 'A brief description of the question enticing users to view it.',
				'value' => $post->_description,
			));			
		}
		$this->setButtons(array("save" => "Post"));
		if(!$this->_isNew) {
			$this->source->setLabel("The Question")->setDescription("Please be as descriptive as possible when asking your question, include as many details as possible, and don't hit people in the face with a wall of text in one huge paragraph.");
			$this->setButtons(array("save" => "Post Question"));
			// Add the Community Post button to the edit only if you're a moderator.
			if(EpicDb_Auth::getInstance()->hasPrivilege(new EpicDb_Auth_Resource_Moderator())) {
				$this->addElement("checkbox", "community", array(
					'label' => 'Community Post?',
					'description' => 'Designate this question as a "Community Post", for more information read the Q&A FAQ.',
					'order' => 200,
				));				
			}
			// var_dump($question->disableRep); exit;
			$this->setDefaults(array("title" => $question->title, "body" => $question->body, "parent" => $question->_parent->_id, "community" => $question->disableRep));
		} else {
			$this->source->setLabel("The Question")->setDescription("Please be as descriptive as possible when asking your question, include as many details as possible, and don't hit people in the face with a wall of text in one huge paragraph.");
			$this->setButtons(array("save" => "Post Question"));
			// If you are creating a post, you can specify if its a community post.
			$this->addElement("checkbox", "community", array(
				'label' => 'Community Post?',
				'description' => 'Designate this question as a "Community Post", for more information read the Q&A FAQ.',
				'order' => 200,
			));
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
		$changedCommunity = false;
		if ($this->community) {
			$flag = !!$this->community->getValue();
			if ( $flag != $question->disableRep ) {
				$changedCommunity = true;
				$question->disableRep = $flag ? true : null;
			}
		}
		if(EpicDb_Auth::getInstance()->hasPrivilege(new EpicDb_Auth_Resource_Moderator())) {
			$question->_featured = (bool) $this->featured->getValue();
			if(!$question->_featuredDate) {
				$question->_featuredDate = time();				
			}
			$question->_description = $this->featuredDescription->getValue();
		}
		$return = parent::save();
		if ( $changedCommunity ) {
			foreach( $question->findAnswers() as $answer ) { $answer->save(); }
		}
		return $return;
	}
} // END class R2Db_Form_Post_Comment
