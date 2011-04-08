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
			$downVote->uncast();
		}

		return parent::cast();
	}

	public function isDisabled()
	{
		if (!$this->_post instanceOf EpicDb_Vote_Interface_Votable) return "This object can't be upvoted";
		if ($this->_post->_profile->createReference() == $this->_userProfile->createReference()) {
			return "You can not vote on your own post";
		}
	}

	protected function _postCast()
	{
		if (!$this->_post instanceOf EpicDb_Vote_Interface_UpOnly)
		{  // posts that only have upvotes don't gain rep.
			$this->giveReputationToTarget(10);
		}
		parent::_postCast();
	}
}