<?php
/**
 * EpicDb_Form_Post_Question_Answer
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Form_Post_Question_Answer extends EpicDb_Form_Post
{
	protected $_recordType = 'answer';

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
		if(!MW_Auth::getInstance()->hasPrivilege(new EpicDb_Auth_Resource_Moderator)) {
			$this->removeElement('tags');
		} else {
			$this->tags->setLabel('Tag records as the answer...')->setDescription('Does an item, quest or NPC in the database answer this question? Tag it!');
			$this->setDefaults(array(
				"tags" => $post->tags->getTags('answer-tag'),
			));
		}
		$this->source->setLabel("Your Answer");
		$this->setButtons(array("save" => "Post Answer"));
	}
	public function save() {
		$answer = $this->getPost();
		parent::save();
		$answer->_parent->bump(EpicDb_Auth::getInstance()->getUserProfile());
	}
} // END class EpicDb_Form_Post_Question_Answer