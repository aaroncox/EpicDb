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
		$this->removeElement('tags');
		$this->source->setLabel("Your Answer")->setDescription("Do you have the answer to this question? Post your answer to earn achievements and reputation on EpicAdvice.com!");
	}
} // END class EpicDb_Form_Post_Question_Answer