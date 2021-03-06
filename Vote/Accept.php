<?php
/**
 *
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 */

/**
 *  EpicDb_Vote_Accept
 *
 * undocumented
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 * @version $Id:$
 */
class EpicDb_Vote_Accept extends EpicDb_Vote_Abstract {
	protected $_type = EpicDb_Vote::ACCEPT;

	public function cast()
	{
		// check for an existing accepted answer
		$answers = EpicDb_Mongo::db('answer')->fetchAll(array(
			'_parent' => $this->_post->_parent->createReference(),
			'votes.accept' => array( '$gt' => 0 ),
		));
		foreach ($answers as $accepted) {
			$vote = EpicDb_Vote::factory( $accepted, EpicDb_Vote::ACCEPT, $this->_userProfile );
			if ($vote->hasCast()) {
				$vote->uncast();
			}
		}

		return parent::cast();
	}

	public function isDisabled()
	{
		if ((!$this->_post instanceOf EpicDb_Vote_Interface_Acceptable)) return "This post can't be accepted";
		$author = $this->_post->_parent->tags->getTag("author");
		if ( !$author || $author->createReference() != $this->_userProfile->createReference() ) {
			return "You are not the questions asker.";
		}
	}

	protected function _postCast()
	{
		if (!$this->_post->isReputationDisabled()) {
			if ($this->_post->tags->getTag('author')->createReference() != $this->_userProfile->createReference()) {
				$this->giveReputationToTarget(15);
				$this->giveReputationToVoter(2);
			}			
		}
		parent::_postCast();
	}

}