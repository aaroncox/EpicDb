<?php
/**
 *
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @copyright Copyright (c) 2010 Momentum Workshop, Inc
 */

/**
 *  EpicDb_Vote_Up
 *
 * undocumented
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @copyright Copyright (c) 2010 Momentum Workshop, Inc
 * @version $Id: Up.php 426 2010-12-16 00:58:44Z root $
 */
class EpicDb_Vote_Up extends EpicDb_Vote_Abstract {
	protected $_type = EpicDb_Vote::UP;

	public function cast()
	{
		// check for an existing downvote first
		$downVote = new EpicDb_Vote_Down($this->_userProfile, $this->_post);
		if ($downVote->hasCast()) {
			if ($downVote->date < mktime() - 60*60*2) {
				$this->_error = "You can no longer change your vote";
				return false;
			}
			$downVote->uncast();
		}

		return parent::cast();
	}

	public function isDisabled()
	{
		if (!$this->_post instanceOf EpicDb_Vote_Interface_Votable) return "This object can't be upvoted";
		if ($this->_post->tags->getTag('author') && $this->_post->tags->getTag('author')->createReference() == $this->_userProfile->createReference()) {
			return "You can not vote on your own post";
		}
	}

	protected function _postCast()
	{
		if (!$this->_post instanceOf EpicDb_Vote_Interface_UpOnly && !$this->_post->isReputationDisabled())
		{  // posts that only have upvotes don't gain rep.
			$this->giveReputationToTarget(10);
		}
		if($this->_post instanceOf EpicDb_Interface_Autotweet) {
			$this->_post->autoTweet();
		}
		parent::_postCast();
	}
}