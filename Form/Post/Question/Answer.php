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
		if(!R2Db_Auth::getInstance()->getUser()->isMember(MW_Auth_Group_Super::getInstance())) {
			$this->tags->removeElement();
		} else {
			$this->tags->setLabel('Tag records as the answer...')->setDescription('Does an item, quest or NPC in the database answer this question? Tag it!');
			$this->setDefaults(array(
				"tags" => $post->tags->getTags('answer-tag'),
			));
		}
		$this->source->setLabel("Your Answer")->setDescription("Do you have the answer to this question? Post your answer to earn achievements and reputation on EpicAdvice.com!");
		$this->setButtons(array("save" => "Post Answer"));
	}
	public function save() {
		$me = EpicDb_Auth::getInstance()->getUserProfile();
		$post = $this->getPost();
		if(!$this->_isNew) {
			$post->bump($me);
			EpicDb_Mongo_Revision::makeEditFor($post, $this->reason->getValue());
		} else {
			$post->_created = time();
		}
		if($this->source) {
			$post->source = $this->source->getValue();
			$post->body = $this->source->getRenderedValue();
		}
		$filter = new EpicDb_Filter_TagJSON();
		if ($this->tags) {
			$post->tags->setTags($filter->toArray($this->tags->getValue()),'answer-tag');
		}
		if($post->_parent && $post->_parent->export() != array()) {
			$parentAuthor = $post->_parent->tags->getTag('author')?:$post->_parent->tags->getTag('source');
			if($parentAuthor) {
				$post->tags->tag($parentAuthor, 'responding-to');
				$post->_parent->bump($me);
			}
		}
		return $post->save();
	}
	public function process($data) {
		if($this->isValid($data)) {
			$this->save();
			return true;
		}
		return false;
	}
} // END class EpicDb_Form_Post_Question_Answer